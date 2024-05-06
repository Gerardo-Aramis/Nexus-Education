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

    // Verificar si se recibió el parámetro 'carrera'
    if (isset($_GET['carrera'])) {
        // Obtener la carrera seleccionada
        $carrera = $_GET['carrera'];

        // Consulta SQL para obtener los datos de los estudiantes filtrados por carrera
        $sql = "SELECT email, names, apellidoPaterno, apellidoMaterno, semester 
                FROM [User] 
                WHERE carreerID = ?";
        $params = array($carrera);

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
        // Si no se recibió el parámetro 'carrera', devolver un mensaje de error
        echo json_encode(array("error" => "No se recibió el parámetro 'carrera'."));
    }
?>