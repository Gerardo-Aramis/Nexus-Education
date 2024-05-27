<?php
$serverName = "tcp:nexus-education.database.windows.net,1433";
$connectionInfo = array(
    "Database" => "NexusEducation",
    "UID" => "nexus_admin", 
    "PWD" => "Nxs#1#Edctn", 
    "CharacterSet" => "UTF-8",
    "LoginTimeout" => 30, 
    "Encrypt" => 1, 
    "TrustServerCertificate" => 0
);

// Establecer la conexión a la base de datos
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die("No se pudo establecer la conexión. " . print_r(sqlsrv_errors(), true));
}

// Verificar si se enviaron los parámetros carrera y semestre
if (!isset($_GET['carrera']) || !isset($_GET['semestre'])) {
    die(json_encode(array('error' => 'Faltan parámetros')));
}

// Obtener los valores de carrera y semestre enviados desde el cliente
$carrera = $_GET['carrera'];
$semestre = $_GET['semestre'];

// Consulta para obtener las materias según la carrera y el semestre
$sqlMaterias = "SELECT subjectID, subjectName FROM Subject WHERE carreerID = ? AND semesterNumber = ?";
$params = array($carrera, $semestre);
$stmtMaterias = sqlsrv_query($conn, $sqlMaterias, $params);

if ($stmtMaterias === false) {
    die(json_encode(array('error' => 'Error al ejecutar la consulta: ' . print_r(sqlsrv_errors(), true))));
}

$materias = array();

// Obtener los resultados de la consulta
while ($row = sqlsrv_fetch_array($stmtMaterias, SQLSRV_FETCH_ASSOC)) {
    $materias[] = $row;
}

// Liberar los recursos
sqlsrv_free_stmt($stmtMaterias);
sqlsrv_close($conn);

// Devolver los resultados como JSON
echo json_encode($materias);
?>