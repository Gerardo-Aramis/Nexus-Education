<?php
// Verificar si se proporcionó un ID de archivo
if(isset($_GET['id'])) {
    $fileID = $_GET['id'];

    // Conexión a la base de datos
    $serverName = "25.41.90.44\\SQLEXPRESS"; 
    $connectionOptions = array(
        "Database" => "NexusEducation",
        "UID" => "log_userweb", 
        "PWD" => "nexus123", 
        "CharacterSet" => "UTF-8"
    );

    $conn = sqlsrv_connect($serverName, $connectionOptions);

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
        header("Location: {$fila['filePath']}");
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
