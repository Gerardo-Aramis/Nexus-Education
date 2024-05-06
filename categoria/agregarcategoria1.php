<?php
// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $categoryName = $_POST["nombre"];
    $carrera = $_POST["carrera"];
    $semestre = $_POST["semestre"];
    $materia = $_POST["materia"];

    $serverName = "IA-27";
    $connectionInfo = array(
    "Database"=> "NexusEducation",
    "UID"=> "sa",
    "PWD"=> "20SQL22",
    "CharacterSet" => "UTF-8"
);

// Establecer la conexión con la base de datos
$conn = sqlsrv_connect($serverName, $connectionInfo);
if( $conn === false ) {
  echo "No se estableció la conexión. ";
  die(print_r(sqlsrv_errors(), true));
}

    // Preparar la consulta SQL para insertar la categoría
    $sql = "INSERT INTO Category (CategoryName, subjectID) VALUES (?, ?)";
    
    // Preparar los parámetros de la consulta
    $params = array($categoryName, $materia);

    // Ejecutar la consulta con parámetros
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si la consulta fue exitosa
    if ($stmt === false) {
        echo "Error al agregar la categoría: " . print_r(sqlsrv_errors(), true);
    } else {
        echo "Categoría agregada correctamente.";
    }


    // Cerrar la conexión
}
?>
