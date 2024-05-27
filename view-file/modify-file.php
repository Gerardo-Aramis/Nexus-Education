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

include '../subir/api-google/vendor/autoload.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=nexuseducation-39b9da529614.json');

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(['https://www.googleapis.com/auth/drive.file']);

if (isset($_FILES['archivos'])) {
    $tamano = $_FILES['archivos']['size'];
    $nombre = $_FILES['archivos']['name'];
    $extension = pathinfo($nombre, PATHINFO_EXTENSION);
    $materia = $_POST["materia"];
    $categoria = $_POST["categoria"];
    $fileID = $_POST["fileID"];

    // Verificar si materia es un valor numérico
    if (!is_numeric($materia)) {
        $sqlGetSubjectID = "SELECT subjectID FROM Subject WHERE subjectName = ?";
        $paramsGetSubjectID = array($materia);
        $stmtGetSubjectID = sqlsrv_query($conn, $sqlGetSubjectID, $paramsGetSubjectID);
        if ($stmtGetSubjectID === false) {
            die("Error al obtener el subjectID: " . print_r(sqlsrv_errors(), true));
        } else {
            $row = sqlsrv_fetch_array($stmtGetSubjectID, SQLSRV_FETCH_ASSOC);
            $materia = $row['subjectID'];
        }
    }

    // Verificar si categoria es un valor numérico
    if (!is_numeric($categoria)) {
        $sqlGetCategoryID = "SELECT CategoryID FROM Category WHERE categoryName = ?";
        $paramsGetCategoryID = array($categoria);
        $stmtGetCategoryID = sqlsrv_query($conn, $sqlGetCategoryID, $paramsGetCategoryID);
        if ($stmtGetCategoryID === false) {
            die("Error al obtener el CategoryID: " . print_r(sqlsrv_errors(), true));
        } else {
            $row = sqlsrv_fetch_array($stmtGetCategoryID, SQLSRV_FETCH_ASSOC);
            $categoria = $row['CategoryID'];
        }
    }

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
                'mimeType' => $mime_type,
                'uploadType' => 'media'
            )
        );

        $ruta = $resultado->id;

        // Preparar la declaración SQL para actualizar el archivo existente
        $sqlUpdateFile = "UPDATE Files
                            SET [fileName] = ?,
                                fileType = ?,
                                fileSize = ?,
                                filePath = ?,
                                authorizationStatus = 'En Espera',
                                userID = ?,
                                subjectID = ?,
                                CategoryID = ?,
                                fileUploadDate = GETDATE()
                            WHERE fileID = ?";

        // Preparar la declaración
        $paramsUpdateFile = array($nombre, strtoupper($extension), $tamano, $ruta, $userID, $materia, $categoria, $fileID);
        $stmtUpdateFile = sqlsrv_query($conn, $sqlUpdateFile, $paramsUpdateFile);

        // Verificar si la consulta fue exitosa
        if ($stmtUpdateFile === false) {
            die("Error al modificar el archivo: " . print_r(sqlsrv_errors(), true));
        } 
        
        header("Location: my-files.php");

    } catch (Google_Service_Exception $gs) {
        $mensaje = json_decode($gs->getMessage());
        echo "Google Service Error: " . $mensaje->error->message;
    } catch (Exception $e) {
        echo "General Error: " . $e->getMessage();
    }
} else {
    die("No se ha enviado ningún archivo.");
}

// Cerrar la conexión a la base de datos
sqlsrv_close($conn);

?>
