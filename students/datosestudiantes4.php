<?php
    // Establecer la conexión con la base de datos
    $serverName = "IA-27";
    $connectionInfo = array(
        "Database"=> "NexusEducation",
        "UID"=> "sa",
        "PWD"=> "20SQL22",
        "CharacterSet" => "UTF-8"
    );
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    // Verificar si se recibieron los parámetros 'carrera' y 'semestre'
    if (isset($_GET['carrera']) && isset($_GET['semestre'])) {
        // Obtener la carrera y semestre seleccionados
        $carrera = $_GET['carrera'];
        $semestre = $_GET['semestre'];

        // Consulta SQL para obtener los datos de los estudiantes filtrados por carrera y semestre
        $sql = "SELECT email, names, apellidoPaterno, apellidoMaterno, semester 
                FROM [User] 
                WHERE carreerID = ? AND semester = ?";
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