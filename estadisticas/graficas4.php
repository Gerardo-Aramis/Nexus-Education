<?php
    // Conexión a la base de datos
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

    // Consulta para obtener la cantidad de archivos por materia
    $sql = "SELECT TOP 5 Subject.subjectName, COUNT(*) AS fileCount 
    FROM Files 
    INNER JOIN Subject ON Files.subjectID = Subject.subjectID
    GROUP BY Subject.subjectName
    ORDER BY COUNT(*) DESC"; // Limita los resultados a las 5 materias con más archivos
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