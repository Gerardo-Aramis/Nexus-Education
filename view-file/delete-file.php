<?php
// Verificar si se ha iniciado sesión
session_start();

$serverName = "IA-27";
$connectionInfo = array(
    "Database" => "NexusEducation",
    "UID" => "sa",
    "PWD" => "20SQL22",
    "CharacterSet" => "UTF-8"
);

// Establecer la conexión a la base de datos
$conn = sqlsrv_connect($serverName, $connectionInfo);

// Verificar la conexión
if ($conn === false) {
    die("No se pudo establecer la conexión: " . print_r(sqlsrv_errors(), true));
}

echo $conn;

// Obtener el userID del usuario si está en sesión
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sqlGetUserID = "SELECT userID FROM [User] WHERE email = ?";
    $paramsGetUserID = array($email);
    $stmtGetUserID = sqlsrv_query($conn, $sqlGetUserID, $paramsGetUserID);
    if ($stmtGetUserID === false) {
        die("Error al obtener el userID: " . print_r(sqlsrv_errors(), true));
    } else {
        $row = sqlsrv_fetch_array($stmtGetUserID, SQLSRV_FETCH_ASSOC);
        $userID = $row['userID'];
    }
} else {
    die("Error: No se encontró la sesión del usuario.");
}
echo "Email de sesión: " . $_SESSION['email'] . "<br>";

echo "ID USER: " . $userID . "<br>";

// Obtener los datos enviados desde el script JavaScript
$nombreArchivo = $_POST['fileName'];
$fechaPublicacion = $_POST['fileUploadDate'];
echo "Nombre de archivo: " . $nombreArchivo . "<br>";

// Consulta para actualizar el estado del archivo a 'Eliminado'
$sqlEliminarArchivo = "UPDATE Files SET authorizationStatus = 'Eliminado' WHERE [fileName] = ? AND userID = ? AND fileUploadDate = ?";
$params = array($nombreArchivo, $userID, $fechaPublicacion);
$stmtEliminarArchivo = sqlsrv_query($conn, $sqlEliminarArchivo, $params);

if ($stmtEliminarArchivo === false) {
    die("Error al actualizar el estado del archivo: " . print_r(sqlsrv_errors(), true));
} else {
    // Redireccionar después de eliminar el archivo
    header("Location: view-file.html");
}

// Cerrar la conexión y liberar recursos
sqlsrv_free_stmt($stmtGetUserID);
sqlsrv_free_stmt($stmtEliminarArchivo);
sqlsrv_close($conn);
?>
