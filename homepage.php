<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Inicio - Nexus Education</title>
  <link rel="stylesheet" href="estilospantallas2.css">
  <link href="images/logo.png" rel="shortcut icon">

  <style>

  </style>
</head>
<body>

    <div class="container">
        
        
    <div class="top-section">
        <img src="images/tecnm.png" alt="Logo Tecnm" class="logo-tecnm">
        <img src="images/ittepic.png" alt="Logo Tec" class="logo-ittepic">
        <img src="images/nexus2.png" alt="Logo Nexus" class="logo-nexus">
        <img src="images/nombrenexus.png" alt="Logo Nombre Nexus" class="logo-nombrenexus">

    </div>

    <div class="left-section" > 
        <!-- SE SUSTITUYE EL HTML HACIA DONDE SE QUIERE REDIRECCIONAR -->
        <div>
            <img src="images/Mi_Perfil.png" alt="Mi_perfil" class="fotosperfil">
            <a href="profile.php">Mi perfil</a>
        </div>
        <div>
            <img style="margin-top: 30%;" src="images/Subir.png" alt="Subir" class="fotosperfil ">
            <a href="subir/SubirArchivo.html">Subir</a>
        </div>
        <div>
            <img style="margin-top: 60%;" src="images/Mis_archivos.png" alt="Mis_archivos" class="fotosperfil">
            <a href="view-file/my-files.php">Mis Archivos</a>
        </div>
        <div>
            <img style="margin-top: 200%;" src="images/Cerrar_sesion.png" alt="Cerrar_sesion" class="fotosperfil"> 
            <a  style="margin-top: 135%;"  href="login.html">Cerrar sesión</a>
        </div>
        <?php
session_start();

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

// Obtener el userID y la carrera del usuario si está en sesión
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sqlGetUserInfo = "SELECT userID, carreerID FROM [User] WHERE email = ?";
    $paramsGetUserInfo = array($email);
    $stmtGetUserInfo = sqlsrv_query($conn, $sqlGetUserInfo, $paramsGetUserInfo);
    if ($stmtGetUserInfo === false) {
        die("Error al obtener la información del usuario: " . print_r(sqlsrv_errors(), true));
    } else {
        $row = sqlsrv_fetch_array($stmtGetUserInfo, SQLSRV_FETCH_ASSOC);
        $userID = $row['userID'];
        $carreerID = $row['carreerID'];

        // Consulta para obtener el nombre de la carrera
        $sqlGetCarreerName = "SELECT carreerName FROM Carreer WHERE carreerID = ?";
        $paramsGetCarreerName = array($carreerID);
        $stmtGetCarreerName = sqlsrv_query($conn, $sqlGetCarreerName, $paramsGetCarreerName);
        if ($stmtGetCarreerName === false) {
            die("Error al obtener el nombre de la carrera: " . print_r(sqlsrv_errors(), true));
        } else {
            $rowCarreer = sqlsrv_fetch_array($stmtGetCarreerName, SQLSRV_FETCH_ASSOC);
            $carreerName = $rowCarreer['carreerName'];

            // Consulta SQL para obtener las rutas de los tres últimos archivos subidos relacionados con el subjectID
            
            $sqlGetRecentFiles = "
            SELECT filePath 
            FROM Files 
            WHERE subjectID IN (SELECT subjectID FROM Subject WHERE carreerID = ?) 
            ORDER BY fileID DESC
            OFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY
        ";
        $paramsGetRecentFiles = array($carreerID);
        $stmtGetRecentFiles = sqlsrv_query($conn, $sqlGetRecentFiles, $paramsGetRecentFiles);
        
        if ($stmtGetRecentFiles === false) {
            die("Error al obtener los archivos recientes: " . print_r(sqlsrv_errors(), true));
        } else {
            // Iterar sobre los resultados
            
        }
        }
    }
} else {
    die("Error: No se encontró la sesión del usuario.");
}

// Cerrar la conexión
sqlsrv_close($conn);
?>
    </div>
 <!-- Parte azul de enmedio -->
    <div class="bottom-section">
    <!--Frase -->   
        <div>
            <h3 >"La vida debe ser una incesante educación"</h3>
            <h4 style="margin-bottom: -2.5%;  font-style: italic; margin-left: 9%; margin-top: -1% ; color:rgb(255, 255, 255); ">Gustavo Flaubert</h4>
       </div>

     <!-- Botones búsqueda y filtrado -->
       
       <div>
            <input style="background-color: #ffffff; display: inline-block;" type="archivo" id="archivo" name="archivo" placeholder="Buscar" required>
            <button style=" display: inline-block; margin-left: -40px; background-image: url('images/buscar.png'); background-size: cover; background-repeat: no-repeat; border: none; background-color: transparent; border-radius: 0;"> </button>
            <button style=" display: inline-block; margin-left: -60px; background-image: url('images/image.png'); background-size: cover; background-repeat: no-repeat; border: none; background-color: transparent; border-radius: 0;"> </button>
        </div>
    
        <!-- Lista de archivos recientes -->
        <div class="recent-files">
            <!-- Lista de archivos recientes -->
        <div style="margin-top: -25px;" class="recent-files">
            <h2 style="text-align: center;">Subidos recientemente en <?php echo $carreerName; ?></h2> <!--Ejemplos -->

            <ul>
         
                  <li>
                  <?php

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

                    // Obtener el userID y la carrera del usuario si está en sesión
                    if (isset($_SESSION['email'])) {
                        $email = $_SESSION['email'];
                        $sqlGetUserInfo = "SELECT userID, carreerID FROM [User] WHERE email = ?";
                        $paramsGetUserInfo = array($email);
                        $stmtGetUserInfo = sqlsrv_query($conn, $sqlGetUserInfo, $paramsGetUserInfo);
                        if ($stmtGetUserInfo === false) {
                            die("Error al obtener la información del usuario: " . print_r(sqlsrv_errors(), true));
                        } else {
                            $row = sqlsrv_fetch_array($stmtGetUserInfo, SQLSRV_FETCH_ASSOC);
                            $userID = $row['userID'];
                            $carreerID = $row['carreerID'];

                            // Consulta para obtener el nombre de la carrera
                            $sqlGetCarreerName = "SELECT carreerName FROM Carreer WHERE carreerID = ?";
                            $paramsGetCarreerName = array($carreerID);
                            $stmtGetCarreerName = sqlsrv_query($conn, $sqlGetCarreerName, $paramsGetCarreerName);
                            if ($stmtGetCarreerName === false) {
                                die("Error al obtener el nombre de la carrera: " . print_r(sqlsrv_errors(), true));
                            } else {
                                $rowCarreer = sqlsrv_fetch_array($stmtGetCarreerName, SQLSRV_FETCH_ASSOC);
                                $carreerName = $rowCarreer['carreerName'];

                                // Consulta SQL para obtener las rutas de los tres últimos archivos subidos relacionados con el subjectID
                                
                                $sqlGetRecentFiles = "
                                SELECT filePath, fileName
                                FROM Files 
                                WHERE subjectID IN (SELECT subjectID FROM Subject WHERE carreerID = ?) 
                                ORDER BY fileID DESC
                                OFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY
                            ";
                            $paramsGetRecentFiles = array($carreerID);
                            $stmtGetRecentFiles = sqlsrv_query($conn, $sqlGetRecentFiles, $paramsGetRecentFiles);
                            
                            if ($stmtGetRecentFiles === false) {
                                die("Error al obtener los archivos recientes: " . print_r(sqlsrv_errors(), true));
                            } else {
                                // Iterar sobre los resultados
                                echo "Los enlaces de los últimos archivos subidos relacionados con la carrera de $carreerName son:<br>";
                                while ($row = sqlsrv_fetch_array($stmtGetRecentFiles, SQLSRV_FETCH_ASSOC)) {
                                    $nombre_archivo = $row['fileName'];
                                    $archivo_drive_id = $row['filePath']; // Enlace directo a Google Drive
                                    // Extraer el ID del archivo del enlace de Google Drive
                                   // $archivo_drive_id = substr($enlace_drive, strpos($enlace_drive, '=') + 1);
                                    // Construir la URL completa para acceder al archivo
                                    $enlace_drive = "https://drive.google.com/file/d/$archivo_drive_id/preview"; 
                                    echo "<p> $nombre_archivo</p>";
                                    echo "<iframe src='$enlace_drive' width='100%' height='500px'></iframe>"; // Cargar el archivo dentro del iframe
                    
                                }
                                echo "</ul>";
                            }
                            }
                        }
                    } else {
                        die("Error: No se encontró la sesión del usuario.");
                    }

                    // Cerrar la conexión
                    sqlsrv_close($conn);
                    ?>

                      
                    </li>
                  
                <!-- Agrega más elementos li según sea necesario -->
            </ul>

        </div>
         <!-- Lista de archivos recomendados -->
        <div class="recommended-files">
        <h2 style="text-align: center;">Te podría interesar...</h2>

        <ul class="iframe-list">
            <li>
            <?php

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

// Obtener el userID y la carrera del usuario si está en sesión
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sqlGetUserInfo = "SELECT userID, carreerID FROM [User] WHERE email = ?";
    $paramsGetUserInfo = array($email);
    $stmtGetUserInfo = sqlsrv_query($conn, $sqlGetUserInfo, $paramsGetUserInfo);
    if ($stmtGetUserInfo === false) {
        die("Error al obtener la información del usuario: " . print_r(sqlsrv_errors(), true));
    } else {
        $row = sqlsrv_fetch_array($stmtGetUserInfo, SQLSRV_FETCH_ASSOC);
        $userID = $row['userID'];
        $carreerID = $row['carreerID'];

        // Consulta para obtener el nombre de la carrera
        $sqlGetCarreerName = "SELECT carreerName FROM Carreer WHERE carreerID = ?";
        $paramsGetCarreerName = array($carreerID);
        $stmtGetCarreerName = sqlsrv_query($conn, $sqlGetCarreerName, $paramsGetCarreerName);
        if ($stmtGetCarreerName === false) {
            die("Error al obtener el nombre de la carrera: " . print_r(sqlsrv_errors(), true));
        } else {
            $rowCarreer = sqlsrv_fetch_array($stmtGetCarreerName, SQLSRV_FETCH_ASSOC);
            $carreerName = $rowCarreer['carreerName'];

            // Consulta SQL para obtener las rutas de los tres últimos archivos subidos relacionados con el subjectID
            
            $sqlGetRecentFiles = "
            SELECT TOP 3 filePath, fileName
            FROM Files 
            WHERE subjectID IN (SELECT subjectID FROM Subject WHERE carreerID = ?) 
            ORDER BY NEWID()
        ";
        $paramsGetRecentFiles = array($carreerID);
        $stmtGetRecentFiles = sqlsrv_query($conn, $sqlGetRecentFiles, $paramsGetRecentFiles);
        
        if ($stmtGetRecentFiles === false) {
            die("Error al obtener los archivos recientes: " . print_r(sqlsrv_errors(), true));
        } else {
            // Iterar sobre los resultados
            while ($row = sqlsrv_fetch_array($stmtGetRecentFiles, SQLSRV_FETCH_ASSOC)) {
                $nombre_archivo = $row['fileName'];
                $archivo_drive_id = $row['filePath']; // Enlace directo a Google Drive
                // Extraer el ID del archivo del enlace de Google Drive
               // $archivo_drive_id = substr($enlace_drive, strpos($enlace_drive, '=') + 1);
                // Construir la URL completa para acceder al archivo
                $enlace_drive = "https://drive.google.com/file/d/$archivo_drive_id/preview"; 
                echo "<p>$nombre_archivo</p>";
                echo "<iframe src='$enlace_drive' width='100%' height='500px'></iframe>";
            }
            echo "</ul>";
        }
        }
    }
} else {
    die("Error: No se encontró la sesión del usuario.");
}

// Cerrar la conexión
sqlsrv_close($conn);
?>
       
        </li>
        
        </ul>

        </div>
    </div>


  </div>

</body>
</html>
