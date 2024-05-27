<?php
// Verificar si se ha iniciado sesión
session_start();

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
// Verificar la conexión
if ($conn === false) {
    die("No se pudo establecer la conexión: " . print_r(sqlsrv_errors(), true));
}

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

// Obtener los datos enviados desde el script JavaScript
$nombreArchivo = $_POST['fileNameDelete'];
$fechaPublicacion = $_POST['fileUploadDateDelete'];

// Consulta para actualizar el estado del archivo a 'Eliminado'
$sqlEliminarArchivo = "UPDATE Files SET authorizationStatus = 'Eliminado' WHERE [fileName] = ? AND userID = ? AND fileUploadDate = ?";

// Preparar la consulta
$stmtEliminarArchivo = sqlsrv_prepare($conn, $sqlEliminarArchivo, array(&$nombreArchivo, &$userID, &$fechaPublicacion));


// Ejecutar la consulta
if (sqlsrv_execute($stmtEliminarArchivo)) {
    // La actualización fue exitosa
    // Redireccionar después de eliminar el archivo
    header("Location: my-files.php");
    exit;
} else {
    // La actualización falló
    die("Error al actualizar el estado del archivo: " . print_r(sqlsrv_errors(), true));
}

// Cerrar la consulta
sqlsrv_free_stmt($stmtEliminarArchivo);
?>
