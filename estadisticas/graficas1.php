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

    $sql = "SELECT Carreer.carreerName, COUNT(*) AS userCount FROM [User] INNER JOIN Carreer ON [User].carreerID = Carreer.carreerID GROUP BY Carreer.carreerName;";
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