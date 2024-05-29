<?php
// Verificar si se proporcionó un ID de archivo
if(isset($_GET['id'])) {
    $fileID = $_GET['id'];

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

    // Verificar si la conexión se estableció correctamente
    if ($conn === false) {
        echo "No se pudo establecer la conexión a la base de datos.";
        die(print_r(sqlsrv_errors(), true));
    }

    // Consulta SQL para obtener la información del archivo
    $consulta = "SELECT * FROM Files WHERE fileID = ?";
    $params = array($fileID);
    $resultado = sqlsrv_query($conn, $consulta, $params);

    if ($resultado === false) {
        echo "Ha ocurrido un error al obtener la información del archivo.";
        die(print_r(sqlsrv_errors(), true));
    }

    // Verificar si se encontró el archivo
    if(sqlsrv_has_rows($resultado)) {
        // Obtener la información del archivo
        $fila = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC);
        
        // Redirigir al usuario al enlace del archivo en Drive
        header("Location: https://drive.google.com/file/d/{$fila['filePath']}/view");
        exit;
    } else {
        echo "El archivo no fue encontrado.";
    }

    // Cerrar la conexión a la base de datos
    sqlsrv_close($conn);
} else {
    echo "No se especificó el ID del archivo.";
}
?>
