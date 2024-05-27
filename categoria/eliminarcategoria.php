<?php
var_dump($_POST);

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
    echo "No se pudo establecer la conexión.";
    die(print_r(sqlsrv_errors(), true));
}

// Verificar si se recibió el nombre de la categoría a eliminar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre_categoria"])) {
    // Recuperar el nombre de la categoría a eliminar
    $nombreCategoria = $_POST["nombre_categoria"];

    // Preparar la consulta SQL para eliminar la categoría por nombre
    $sql = "DELETE FROM Category WHERE CategoryName = ?";

    // Preparar los parámetros de la consulta
    $params = array($nombreCategoria);

    // Ejecutar la consulta con parámetros
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si hubo errores al ejecutar la consulta
    if ($stmt === false) {
        echo "Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true);
    } else {
        // Verificar si se afectó alguna fila en la base de datos
        $rowsAffected = sqlsrv_rows_affected($stmt);
        if ($rowsAffected === false) {
            echo "No se pudo determinar si se afectaron filas.";
        } elseif ($rowsAffected == 0) {
            echo "No se encontró ninguna categoría con el nombre proporcionado.";
        } else {
            echo "Categoría eliminada correctamente.";
        }
    }

    // Cerrar la consulta y la conexión
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
} else {
    echo "No se recibió el nombre de la categoría.";
}
?>
