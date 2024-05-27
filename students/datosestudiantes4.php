<?php
    // Establecer la conexión con la base de datos
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

    // Verificar si se recibieron los parámetros 'carrera' y 'semestre'
    if (isset($_GET['carrera']) && isset($_GET['semestre'])) {
        // Obtener la carrera y semestre seleccionados
        $carrera = $_GET['carrera'];
        $semestre = $_GET['semestre'];

        // Consulta SQL para obtener los datos de los estudiantes filtrados por carrera y semestre
        $sql = "SELECT noCtrol, semester, names, apellidoPaterno, apellidoMaterno,
        (SELECT typeName FROM UserType WHERE userTypeID = U.userTypeID) AS TipoUsuario, email 
        FROM [User] U WHERE carreerID = ? AND semester = ?";
        $params = array($carrera, $semestre);

        // Ejecutar la consulta SQL con los parámetros correspondientes
        $stmt = sqlsrv_query($conn, $sql, $params);

        // Almacenar los resultados en un array
        $resultados = array();
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $resultados[] = $row;
        }

        // Devolver los resultados como un JSON
        echo json_encode($resultados);
    } else {
        // Si no se recibieron los parámetros necesarios, devolver un mensaje de error
        echo json_encode(array("error" => "No se recibieron los parámetros necesarios."));
    }
?>