<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Mis Archivos - Nexus Education</title>
  <link rel="stylesheet" href="style-my-files.css">
  <link href="images/logo.png" rel="shortcut icon">

  <style>

  </style>
</head>
<body>
    <div class="container">
 
<!-- PARTE DE ARRIBA - TITULOS -->
    <div class="top-section">
        <a href="../homepage.php">
            <img src="../images/nexus.png" alt="Logo Nexus" class="logo-nexus">
        </a>
        <h1 > Mis archivos </h1>

    </div>

<!-- BARRA DE LA IZQUIERDA -->

    <div class="left-section" > 
        <!-- SE SUSTITUYE EL HTML HACIA DONDE SE QUIERE REDIRECCIONAR -->
        <div>
            <img src="../images/Mi_Perfil.png" alt="Mi_perfil" class="fotosperfil">
            <a href="../profile.php">Mi perfil</a>
        </div>
        <div>
            <img style="margin-top: 30%;" src="../images/Subir.png" alt="Subir" class="fotosperfil ">
            <a href="../subir/SubirArchivo.html">Subir</a>
        </div>
        <div>
            <img style="margin-top: 60%;" src="../images/Mis_archivos.png" alt="Mis_archivos" class="fotosperfil">
            <a href="my-files.php">Mis Archivos</a>
        </div>
        <div>
            <img style="margin-top: 85%;" src="../images/buscar.png" alt="Mis_archivos" class="fotosperfil">
            <a href="buscar.html">Buscar</a>
        </div>
        <div>
            <img style="margin-top: 200%;" src="../images/Cerrar_sesion.png" alt="Cerrar_sesion" class="fotosperfil"> 
            <a  style="margin-top: 110%;"  href="../login.html">Cerrar sesión</a>
        </div>

    </div>

 <!-- PARTE AZUL DE CONTENIDO -->

    <div class="bottom-section">
        <div>
            <input style="background-color: #ffffff; display: inline-block;" type="archivo" id="archivo" name="archivo" placeholder="Buscar" required>
            <button style=" display: inline-block; margin-left: -40px; background-image: url('images/buscar.png'); background-size: cover; background-repeat: no-repeat; border: none; background-color: transparent; border-radius: 0;"> </button>
        </div>
        
        <div>
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

                    // Consulta SQL para obtener los archivos subidos por el usuario
                    $sqlGetUserFiles = "SELECT fileType, fileName, filePath FROM Files WHERE userID = ?";
                    $paramsGetUserFiles = array($userID);
                    $stmtGetUserFiles = sqlsrv_query($conn, $sqlGetUserFiles, $paramsGetUserFiles);

                    if ($stmtGetUserFiles === false) {
                        die("Error al obtener los archivos del usuario: " . print_r(sqlsrv_errors(), true));
                    } else {
                        // Inicio de la tabla
                        echo "<table class='sin-borde'>";
                        echo "<tbody>";

                        // Iterar sobre los resultados y construir las filas de la tabla
                        while ($row = sqlsrv_fetch_array($stmtGetUserFiles, SQLSRV_FETCH_ASSOC)) {
                            $fileType = $row['fileType'];
                            $fileName = $row['fileName'];
                            $filePath = $row['filePath'];

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
                                case "XLS":
                                    $imageSrc = "../images/Archivo_Xls.png";
                                    break;
                                // Agregar más casos según los tipos de archivo que tengas
                                default:
                                    $imageSrc = "../images/Archivo_Desconocido.png"; // Si el tipo de archivo es desconocido
                            }

                            // Imprimir la fila de la tabla con el ícono correspondiente y el nombre del archivo
                            echo "<tr>";
                            echo "<td><a href='view-file.html?id=$filePath&nombre=$fileName'>";
                            echo "<img src='$imageSrc' alt='Archivo' class='fotosarchivo'>";
                            echo "</a></td>";
                            echo "<td>$fileName</td>";
                            echo "</tr>";
                        }

                        // Fin de la tabla
                        echo "</tbody>";
                        echo "</table>";
                    }
                }
            }

            // Cerrar la conexión
            sqlsrv_close($conn);
            ?>

        </div>
    </div>


  </div>

</body>
</html>
