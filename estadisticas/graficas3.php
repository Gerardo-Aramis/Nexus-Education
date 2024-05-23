<?php
     $serverName = "25.41.90.44\\SQLEXPRESS"; 
     $connectionOptions = array(
         "Database" => "NexusEducation",
         "UID" => "log_userweb", 
         "PWD" => "nexus123", 
         "CharacterSet" => "UTF-8"
     );
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die(json_encode(array("error" => sqlsrv_errors())));
    }

    // Consulta para obtener tipos de usuario y su cantidad
    $sql = "SELECT typeName, COUNT(*) AS userCount FROM [User] INNER JOIN UserType ON [User].userTypeID = UserType.userTypeID GROUP BY typeName";
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