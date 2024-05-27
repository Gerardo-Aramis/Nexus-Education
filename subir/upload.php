<?php

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

include 'api-google/vendor/autoload.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=nexuseducation-39b9da529614.json');

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(['https://www.googleapis.com/auth/drive.file']);

$tamano = $_FILES['archivos']['size'];
$nombre = $_FILES['archivos']['name'];
$extension = pathinfo($nombre, PATHINFO_EXTENSION);
$materia = $_POST["materia"];
$categoria = $_POST["categoria"];

try {
    $service = new Google_Service_Drive($client);
    $file_path = $_FILES['archivos']['tmp_name'];

    $file = new Google_Service_Drive_DriveFile();
    $file->setName($nombre);

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_path);

    $file->setParents(array("1pDJDSIIqZ01ZFrcnrXeSiS9AyGjPjE6r"));
    $file->setDescription("Archivo cargado desde php");
    $file->setMimeType($mime_type);

    $resultado = $service->files->create(
        $file,
        array(
            'data' => file_get_contents($file_path),
            'mimeType' => "imagen/png",
            'uploadType' => 'media'
        )
    );

    //Parece que solo es quitarle la parte del enlace para conseguir únicamente el id. 
    $ruta =$resultado->id;

    // Preparar la declaración SQL para insertar un nuevo archivo
    $sqlInsertFile = "INSERT INTO [Files] ([fileName], fileType, fileSize, [filePath], authorizationStatus, userID, subjectID, CategoryID, fileUploadDate) VALUES (?, ?, ?, ?, 'En Espera', ?, ?, ?, GETDATE())";

    // Preparar la declaración
    $paramsInsertFile = array($nombre, strtoupper($extension), $tamano, $ruta, $userID, $materia, $categoria);
    $stmtInsertFile = sqlsrv_query($conn, $sqlInsertFile, $paramsInsertFile);

    // Verificar si la consulta fue exitosa
    if ($stmtInsertFile === false) {
        die("Error al insertar el archivo: " . print_r(sqlsrv_errors(), true));
    } 
    
    header("Location: SubirArchivo.html"); ;

} catch (Google_Service_Exception $gs) {
    $mensaje = json_decode($gs->getMessage());
    echo $mensaje->error->message();
} catch (Exception $e) {
    echo $e->getMessage();
}


// Cerrar la conexión a la base de datos
sqlsrv_close($conn);

?>
