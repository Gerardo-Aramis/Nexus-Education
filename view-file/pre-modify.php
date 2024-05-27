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
if ($conn === false) {
    echo "No se estableció la conexión. ";
    die(print_r(sqlsrv_errors(), true));
}

// Verificar si hay una sesión activa
if (isset($_SESSION['email'])) {
    // Obtener el userID del usuario
    $email = $_SESSION['email'];
    $sqlGetUserID = "SELECT userID FROM [User] WHERE email = ?";
    $paramsGetUserID = array($email);
    $stmtGetUserID = sqlsrv_query($conn, $sqlGetUserID, $paramsGetUserID);
    if ($stmtGetUserID === false) {
        die("Error al obtener el userID: " . print_r(sqlsrv_errors(), true));
    } else {
        $row = sqlsrv_fetch_array($stmtGetUserID, SQLSRV_FETCH_ASSOC);
        if ($row === null) {
            die("Error: Usuario no encontrado.");
        }
        $userID = $row['userID'];
    }
} else {
    die("Error: No se encontró la sesión del usuario.");
}

// Validar y obtener los datos de POST
if (!isset($_POST['fileNameModify'], $_POST['fileUploadDateModify'])) {
    die("Error: Datos incompletos.");
}

$nombreArchivo = $_POST['fileNameModify'];
$fechaPublicacion = $_POST['fileUploadDateModify'];

// Consulta para obtener las columnas subjectID, CategoryID y fileName
$sqlConsulta = "SELECT [fileID], [fileName], 
                (SELECT subjectName FROM [Subject] WHERE F.subjectID = subjectID) AS subjectName, 
                (SELECT CategoryName FROM Category WHERE F.CategoryID = CategoryID) AS categoryName,
                (SELECT carreerName FROM Carreer WHERE carreerID = (SELECT carreerID FROM [Subject] S WHERE S.subjectID = F.subjectID)) AS carrerName,
                (SELECT semesterNumber FROM [Subject] S WHERE S.subjectID = F.subjectID) AS semester
                FROM Files F WHERE fileName = ? AND fileUploadDate = ? AND userID = ?";

// Preparar la consulta
$paramsConsulta = array(&$nombreArchivo, &$fechaPublicacion, &$userID);
$stmtConsulta = sqlsrv_prepare($conn, $sqlConsulta, $paramsConsulta);

// Ejecutar la consulta
if (sqlsrv_execute($stmtConsulta)) {
    // Obtener el resultado
    $row = sqlsrv_fetch_array($stmtConsulta, SQLSRV_FETCH_ASSOC);
    if ($row !== null) {
        // Redireccionar a modify-file.html con la información como parámetros GET
        $url = "modify-file.html?subjectName=" . urlencode($row['subjectName']) . 
               "&categoryName=" . urlencode($row['categoryName']) . 
               "&fileName=" . urlencode($row['fileName']) . 
               "&carrerName=" . urlencode($row['carrerName']) . 
               "&semester=" . urlencode($row['semester']) . 
               "&fileID=" . urlencode($row['fileID']); 
        header("Location: $url");
    } else {
        die("Error: No se encontraron datos para los criterios proporcionados.");
    }
} else {
    die(json_encode(array('error' => 'Error al ejecutar la consulta')));
}

// Cerrar la conexión y liberar los recursos
sqlsrv_free_stmt($stmtConsulta);
sqlsrv_close($conn);
?>
