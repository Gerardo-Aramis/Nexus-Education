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
if( $conn === false ) {
  echo "No se estableció la conexión. ";
  die(print_r(sqlsrv_errors(), true));
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

// Si se envió el parámetro "materia", se debe buscar las categorías correspondientes
if (isset($_GET['materia'])) {
    // Obtener el valor de la materia enviada desde el cliente
    $materia = $_GET['materia'];

    // Consulta para obtener las categorías según la materia seleccionada
    $sqlCategorias = "SELECT CategoryName FROM Category WHERE subjectID = ?";
    $params = array($materia);
    $stmtCategorias = sqlsrv_query($conn, $sqlCategorias, $params);

    if ($stmtCategorias === false) {
        die(json_encode(array('error' => 'Error al ejecutar la consulta de categorías: ' . print_r(sqlsrv_errors(), true))));
    }

    $categorias = array();

    // Obtener los resultados de la consulta
    while ($row = sqlsrv_fetch_array($stmtCategorias, SQLSRV_FETCH_ASSOC)) {
        $categorias[] = $row['CategoryName'];
    }

    // Liberar los recursos
    sqlsrv_free_stmt($stmtCategorias);

    // Devolver las categorías como respuesta JSON
    echo json_encode($categorias);
} else {
    // Devolver las materias como respuesta JSON
    echo json_encode($materias);
}

// Cerrar la conexión
sqlsrv_close($conn);
?>