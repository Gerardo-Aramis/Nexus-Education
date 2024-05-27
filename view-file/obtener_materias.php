<?php
//header('Content-Type: text/html; charset=UTF-8');

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

// Obtener los valores de carrera y semestre enviados desde el cliente
$carrera = $_POST['carrera'];
$semestre = $_POST['semestre'];

// Consulta para obtener las materias según la carrera y el semestre
$sqlMaterias = "SELECT subjectID, subjectName FROM [Subject] WHERE carreerID = ? AND semesterNumber = ?";
$params = array($carrera, $semestre);
$stmtMaterias = sqlsrv_query($conn, $sqlMaterias, $params);

// Verificar la ejecución de la consulta
if (!$stmtMaterias) {
    var_dump(sqlsrv_errors()); // Aquí se imprimirán los errores, si los hay
    die(json_encode(array('error' => 'Error al ejecutar la consulta')));
}

// Arreglo para almacenar las materias
$materias = array();

// Obtener las materias
while ($row = sqlsrv_fetch_array($stmtMaterias, SQLSRV_FETCH_ASSOC)) {
    // Agregar la fila al arreglo de materias
    $materias[] = array('value' => $row["subjectID"], 'text' => $row["subjectName"]);
}

// Devolver las materias en formato JSON
$materias_json = json_encode($materias);
echo $materias_json;

// Cerrar la conexión y liberar los recursos
sqlsrv_free_stmt($stmtMaterias);
sqlsrv_close($conn);
?>