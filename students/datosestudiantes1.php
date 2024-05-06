<?php
// Establecer la conexión con la base de datos
$serverName = "IA-27";
$connectionInfo = array(
    "Database"=> "NexusEducation",
    "UID"=> "sa",
    "PWD"=> "20SQL22",
    "CharacterSet" => "UTF-8"
);
$conn = sqlsrv_connect($serverName, $connectionInfo);

// Consulta SQL para obtener los datos de los estudiantes
$sql = "SELECT email, names, apellidoPaterno, apellidoMaterno, semester FROM [User]";
$stmt = sqlsrv_query($conn, $sql);

// Almacenar los resultados en un array
$resultados = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $resultados[] = $row;
}

// Devolver los resultados como un JSON
echo json_encode($resultados);
?>