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

// Verificar si se enviaron los parámetros carrera, semestre y materia
if (!isset($_GET['carrera']) || !isset($_GET['semestre']) || !isset($_GET['materia'])) {
    die(json_encode(array('error' => 'Faltan parámetros')));
}

// Obtener los valores de carrera, semestre y materia enviados desde el cliente
$carrera = $_GET['carrera'];
$semestre = $_GET['semestre'];
$materia = $_GET['materia'];

// Consulta para obtener las categorías según la carrera, semestre y materia
$sqlCategorias = "SELECT CategoryID, CategoryName FROM Category WHERE subjectID IN (SELECT subjectID FROM Subject WHERE carreerID = ? AND semesterNumber = ? AND subjectID = ?)";
$params = array($carrera, $semestre, $materia);
$stmtCategorias = sqlsrv_query($conn, $sqlCategorias, $params);

if ($stmtCategorias === false) {
    die(json_encode(array('error' => 'Error al ejecutar la consulta: ' . print_r(sqlsrv_errors(), true))));
}

$categorias = array();

// Obtener los resultados de la consulta
while ($row = sqlsrv_fetch_array($stmtCategorias, SQLSRV_FETCH_ASSOC)) {
    $categorias[] = $row;
}

// Liberar los recursos
sqlsrv_free_stmt($stmtCategorias);
sqlsrv_close($conn);

// Devolver los resultados como JSON
echo json_encode($categorias);
?>