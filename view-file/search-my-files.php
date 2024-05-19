<?php
    session_start(); // Iniciar la sesión

    $serverName = "IA-27";
    $connectionInfo = array(
        "Database"=> "NexusEducation",
        "UID"=> "sa",
        "PWD"=> "20SQL22",
        "CharacterSet" => "UTF-8"
    );

    // Establecer la conexión a la base de datos
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    // Verificar la conexión
    if ($conn === false) {
        die("No se pudo establecer la conexión: " . print_r(sqlsrv_errors(), true));
    }

    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $sqlGetUserInfo = "SELECT userID FROM [User] WHERE email = ?";
        $paramsGetUserInfo = array($email);
        $stmtGetUserInfo = sqlsrv_query($conn, $sqlGetUserInfo, $paramsGetUserInfo);
        if ($stmtGetUserInfo === false) {
            die("Error al obtener la información del usuario: " . print_r(sqlsrv_errors(), true));
        } else {
            $row = sqlsrv_fetch_array($stmtGetUserInfo, SQLSRV_FETCH_ASSOC);
            $userID = $row['userID'];

            // Consulta SQL para obtener los archivos subidos por el usuario y coincidentes con el término de búsqueda
            $sqlSearchFiles = "SELECT fileType, fileName, filePath, authorizationStatus, fileUploadDate FROM Files WHERE userID = ? AND fileName LIKE ? AND (authorizationStatus = 'Aceptado' OR 
            authorizationStatus = 'En Espera' OR authorizationStatus = 'Rechazado') ORDER BY fileUploadDate DESC";
            $paramsSearchFiles = array($userID, '%' . $_GET['term'] . '%');
            $stmtSearchFiles = sqlsrv_query($conn, $sqlSearchFiles, $paramsSearchFiles);

            if ($stmtSearchFiles === false) {
                die("Error al buscar archivos: " . print_r(sqlsrv_errors(), true));
            } else {
                // Inicio de la tabla de resultados
                $output = "<table id='tabla class='sin-borde'>";
                $output .= "<thead>";
                $output .=  "<tr>";
                $output .=  "<th>Tipo archivo</th>";
                $output .=  " <th>Nombre del Archivo</th>";
                $output .=  " <th>Estatus de Autorización</th>";
                $output .=  "<th>Fecha de Publicación</th>";
                $output .=  "</tr>";
                $output .=  "</thead>";
                $output .= "<tbody>";

                // Iterar sobre los resultados y construir las filas de la tabla
                while ($row = sqlsrv_fetch_array($stmtSearchFiles, SQLSRV_FETCH_ASSOC)) {
                    $fileType = $row['fileType'];
                    $fileName = $row['fileName'];
                    $filePath = $row['filePath'];
                    $authorizationStatus = $row['authorizationStatus'];
                    $fileUploadDate = $row['fileUploadDate']->format('Y-m-d H:i:s');

                    // Determinar la imagen del archivo según el tipo de archivo
                    $imageSrc = "";
                    switch ($fileType) {
                        case "PDF":
                            $imageSrc = "../images/Archivo_Pdf.png";
                            break;
                        case "DOCX":
                            $imageSrc = "../images/Archivo_Word.png";
                            break;
                        case "PPTX":
                            $imageSrc = "../images/Archivo_Powerpoint.png";
                            break;
                        case "XLSX":
                            $imageSrc = "../images/Archivo_Xls.png";
                            break;
                        // Agregar más casos según los tipos de archivo que tengas
                        default:
                            $imageSrc = "../images/Archivo_Desconocido.png"; // Si el tipo de archivo es desconocido
                    }

                    // Construir la fila de la tabla con el ícono correspondiente y el nombre del archivo
                    $output .= "<tr>";
                    $output .= "<td><a href='view-file.html?id=$filePath&nombre=$fileName'>";
                    $output .= "<img src='$imageSrc' alt='Archivo' class='fotosarchivo'>";
                    $output .= "</a></td>";
                    $output .= "<td>$fileName</td>";
                    $output .= "<td>$authorizationStatus</td>";
                    $output .= "<td>$fileUploadDate</td>";
                    $output .= "</tr>";
                }

                // Fin de la tabla de resultados
                $output .= "</tbody>";
                $output .= "</table>";

                // Devolver los resultados de la búsqueda
                echo $output;
            }
        }
    }

    // Cerrar la conexión
    sqlsrv_close($conn);
?>
