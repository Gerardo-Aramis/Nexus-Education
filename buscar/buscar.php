<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Pantalla principal</title>
  <link rel="stylesheet" href="estilospantallas.css">

  <style>
    .archivo-container {
      text-align: center;
    }

    .archivo-container button {
        padding: 10px 20px; /* Ajusta el padding para aumentar el tamaño del botón */
        font-size: 14px; /* Ajusta el tamaño del texto del botón */
        white-space: nowrap; /* Evita que el texto se divida en varias líneas */
        overflow: hidden; /* Oculta cualquier parte del texto que no quepa */
        text-overflow: ellipsis; /* Agrega puntos suspensivos al final del texto si no cabe */
        margin: 10px; /* Añade un margen para separar los botones */
        min-width: 120px; /* Establece un ancho mínimo para el botón */
    }
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
            <a href="../profile.php">Mi perfil</a>
        </div>
        <div>
            <img style="margin-top: 30%;" src="images/Subir.png" alt="Subir" class="fotosperfil ">
            <a href="../subir/SubirArhivo.html">Subir</a>
        </div>
        <div>
            <img style="margin-top: 60%;" src="images/Mis_archivos.png" alt="Mis_archivos" class="fotosperfil">
            <a href="../view-file/my-files.php">Mis Archivos</a>
        </div>
        <div>
            <img style="margin-top: 85%;" src="images/buscar.png" alt="Mis_archivos" class="fotosperfil">
            <a href="buscar.php">Buscar</a>
        </div>
        <div>
            <img style="margin-top: 200%;" src="images/Cerrar_sesion.png" alt="Cerrar_sesion" class="fotosperfil"> 
            <a  style="margin-top: 105%;"  href="../login.html">Cerrar sesión</a>
        </div>

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
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input style="background-color: #ffffff; display: inline-block;" type="archivo" id="archivo" name="archivo" placeholder="Buscar" required>
                <button style="display: inline-block; background-image: url('images/buscar.png'); background-size: cover; background-repeat: no-repeat; border: none; background-color: transparent; border-radius: 0;"></button>
            </form>
        </div>

        <!-- PHP para procesar la búsqueda -->
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['archivo'])) {
            // Conexión a la base de datos SQL Server

            $serverName = "25.41.90.44\\SQLEXPRESS"; 
            $connectionOptions = array(
                "Database" => "NexusEducation",
                "UID" => "log_userweb", 
                "PWD" => "nexus123", 
                "CharacterSet" => "UTF-8"
            );

            $conn = sqlsrv_connect($serverName, $connectionOptions);

            // Verificar la conexión
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            $termino_busqueda = $_POST['archivo'];

            // Consulta SQL para buscar coincidencias en la columna fileName
            $sql = "SELECT * FROM [Files] WHERE [fileName] LIKE '%$termino_busqueda%' and authorizationStatus = 'Aceptado'";
            $stmt = sqlsrv_query($conn, $sql);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // Mostrar resultados
            echo "<h3>Resultados de la búsqueda:</h3>";
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $nombre_archivo = $row['fileName'];
                $filePath = $row['filePath']; // Enlace directo a Google Drive
                $archivoID =  $row['fileID'];
                // Extraer el ID del archivo del enlace de Google Drive
                //$archivo_drive_id = substr($enlace_drive, strpos($enlace_drive, '=') + 1);
                // Construir la URL completa para acceder al archivo
                $enlace_drive = "https://drive.google.com/file/d/$filePath/preview"; 
                echo "<div class='archivo-container'>";
                echo "<p>$nombre_archivo</p>";
                echo "<div class='iframe-container'><iframe src='$enlace_drive' width='60%' height='500px'></iframe></div>"; // Cargar el archivo dentro del iframe

                // Construir la URL completa para descargar el archivo
                $enlace_descarga = "https://drive.google.com/uc?export=download&id=$filePath"; 
                echo "<a href='$enlace_descarga'><button>Descargar</button></a>";
                
                // Botón para agregar comentario
                echo "<form action='agregar_comentario.php' method='POST'>";
                echo "<input type='hidden' name='archivo_id' value='$archivoID'>"; // Pasar el ID del archivo como valor oculto
                echo "<button type='submit'>Comentario</button>";
                echo "</form>";
                echo "</div>";
            }

            // Liberar recursos
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);
        }
        ?>
    </div>


  </div>

</body>
</html>
