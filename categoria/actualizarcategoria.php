<?php
$serverName = "25.41.90.44\\SQLEXPRESS"; 
$connectionOptions = array(
    "Database" => "NexusEducation",
    "UID" => "log_userweb", 
    "PWD" => "nexus123", 
    "CharacterSet" => "UTF-8"
);

// Establecer la conexión con la base de datos
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die("No se pudo establecer la conexión. " . print_r(sqlsrv_errors(), true));
}

// Verificar si se enviaron los parámetros necesarios
if (!isset($_POST['categoria'])) {
    die(json_encode(array('error' => 'No se recibió la categoría')));
}

$categoria = $_POST['categoria'];
$materia = $_POST['materia'];
$newName = isset($_POST['newName']) ? $_POST['newName'] : null;

// Actualizar la base de datos
$sqlUpdate = "UPDATE Category SET subjectID = ?";

// Si se proporciona un nuevo nombre, actualizar también el nombre de la categoría
if (!empty($newName)) {
    $sqlUpdate .= ", CategoryName = ?";
}

$sqlUpdate .= " WHERE CategoryID = ?";
$params = array($materia);

// Si se proporciona un nuevo nombre, agregarlo a los parámetros
if (!empty($newName)) {
    $params[] = $newName;
}

$params[] = $categoria;

$stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $params);

if ($stmtUpdate === false) {
    die(json_encode(array('error' => 'Error al actualizar la categoría: ' . print_r(sqlsrv_errors(), true))));
}

// Verificar si se realizó alguna actualización
$rowsAffected = sqlsrv_rows_affected($stmtUpdate);
if ($rowsAffected > 0) {
    echo json_encode(array('message' => 'Categoría actualizada correctamente'));
} else {
    echo json_encode(array('message' => 'No se realizaron cambios'));
}

// Liberar los recursos
sqlsrv_free_stmt($stmtUpdate);
sqlsrv_close($conn);
?>