<?php
$serverName = "IA-27";

$connectionInfo = array(
    "Database"=> "NexusEducation",
    "UID"=> "sa",
    "PWD"=> "20SQL22",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);
if( $conn === false ) {
  echo "No se estableció la conexión. ";
  die(print_r(sqlsrv_errors(), true));
}

$subjectID = $_POST['materia'];
// Consulta para obtener las categorías filtradas por el ID de materia
$sqlCategorias = "SELECT CategoryID, CategoryName FROM Category WHERE subjectID = ?";
$params = array($subjectID);
$stmtCategorias = sqlsrv_query($conn, $sqlCategorias, $params);

// Verificar la ejecución de la consulta
if (!$stmtCategorias) {
    die(json_encode(array('error' => 'Error al ejecutar la consulta')));
}

// Arreglo para almacenar las categorías
$categorias = array();

// Obtener las categorías
while ($row = sqlsrv_fetch_array($stmtCategorias, SQLSRV_FETCH_ASSOC)) {
    $categorias[] = array('value' => $row["CategoryID"], 'text' => $row["CategoryName"]);
}

// Cerrar la conexión y liberar los recursos
sqlsrv_free_stmt($stmtCategorias);
sqlsrv_close($conn);

// Devolver las categorías en formato JSON
echo json_encode($categorias);
?>
