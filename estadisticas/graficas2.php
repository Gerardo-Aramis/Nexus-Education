<?php
    // Conexión a la base de datos

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
        die(json_encode(array("error" => sqlsrv_errors())));
    }

    // Consulta para obtener la cantidad de archivos subidos por carrera
    $sql = "SELECT Carreer.carreerName, COUNT(*) AS fileCount FROM Files INNER JOIN [User] ON Files.userID = [User].userID INNER JOIN Carreer ON [User].carreerID = Carreer.carreerID GROUP BY Carreer.carreerName";
    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt === false) {
        die(json_encode(array("error" => sqlsrv_errors())));
    }

    $data = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }

    sqlsrv_close($conn);
    echo json_encode($data);
?>
