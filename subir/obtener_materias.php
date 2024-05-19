<?php
//header('Content-Type: text/html; charset=UTF-8');
$serverName = "25.41.90.44\\SQLEXPRESS"; 
$connectionOptions = array(
    "Database" => "NexusEducation",
    "UID" => "log_userweb", 
    "PWD" => "nexus123", 
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
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