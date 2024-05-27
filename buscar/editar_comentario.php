<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Modificar Archivo</title>
  <link rel="stylesheet" href="editarcomentario.css">
  <link rel="icon" href="images/nexus2.png" type="image/x-icon">
  <style>
        textarea {
            height: 200px; /* Altura del textarea */
            width: 60%; /* Ancho del textarea */
            padding: 10px; /* Relleno dentro del textarea */
            font-family: Arial, Helvetica, sans-serif; /* Familia de fuentes */
            font-size: 12px; /* Tamaño de la fuente */
            border: 1px solid #ccc; /* Borde del textarea */
            border-radius: 5px; /* Radio de los bordes del textarea */
            resize: vertical; /* Permitir que el usuario redimensione verticalmente */
            box-sizing: border-box; /* Incluir el relleno y el borde en el tamaño total */
            margin-left: 15%;
        }
        
        .form-group {
            margin-top: 10px;
        }
        
        .form-group button {
            margin-top: 10px;
        }
  </style>
</head>
<body>
    <div class="container">
 
        <!-- PARTE DE ARRIBA - TITULOS -->
        <div class="top-section">
            <a href="../homepage.php">
                <img src="../images/nexus.png" alt="Logo Nexus" class="logo-nexus">
            </a>
            <h1>Editar comentario</h1>
        </div>

        <!-- BARRA DE LA IZQUIERDA -->
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

        <!-- PARTE AZUL DE CONTENIDO -->
        <div class="bottom-section" >
            <div style="display: flex; align-items: center;">
                <!-- Pasa el archivo_id como parámetro en la URL -->
                
            </div>

            <!-- TITULO Y TEXT AREA -->
            <?php
            // Aquí va el código PHP para manejar la edición del comentario
            // Verificar si se ha proporcionado el ID del comentario a través de la URL
            if (isset($_POST['commentID'])) {
                // Obtener el ID del comentario de la URL
                $commentID = $_POST['commentID'];

                // Obtener el ID del archivo al que pertenece el comentario
                if (isset($_POST['archivo_id'])) {
                    $archivo_id = $_POST['archivo_id'];
                } else {
                    echo "No se proporcionó el ID del archivo.";
                    exit(); // Salir del script si no se proporciona el ID del archivo
                }

                // Verificar si se ha enviado el formulario para editar el comentario
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Verificar si se ha proporcionado el nuevo contenido del comentario
                    if (isset($_POST['new_comment_content'])) {
                        // Obtener el nuevo contenido del comentario
                        $newCommentContent = $_POST['new_comment_content'];

                        // Establecer la conexión a la base de datos
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
                        // Verificar la conexión
                        if ($conn === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }

                        // Preparar la consulta para actualizar el comentario
                        $query = "UPDATE Comments SET commentContent = ? WHERE commentsID = ?";
                        $params = array($newCommentContent, $commentID);
                        $stmt = sqlsrv_query($conn, $query, $params);

                        if ($stmt === false) {
                            
                        } else {
                            //echo "¡Comentario actualizado correctamente!";
                            // Redirigir a la página de comentarios después de unos segundos
                            
                        }

                        // Cerrar la conexión a la base de datos
                        sqlsrv_close($conn);
                    } else {
                        //echo "No se proporcionó el nuevo contenido del comentario.";
                    }
                }

            } else {
                // Manejar la situación si no se proporciona el ID del comentario
                echo "No se proporcionó el ID del comentario.";
                // Puedes redirigir a alguna página de error o manejarlo según tus necesidades
                exit(); // Asegura que el script se detenga después de mostrar el mensaje de error
            }
            ?>
            <h2 style="text-align: left; margin-left: 15%;">Editar Comentario</h2>
            
            <div class="form-group">
                <form action="editar_comentario.php" method="POST">
                <textarea name="new_comment_content" id="new_comment_content" class="comment-form" required></textarea>
                <input type="hidden" name="commentID" value="<?php echo $commentID; ?>">
                <input type="hidden" name="archivo_id" value="<?php echo $archivo_id; ?>">
                <button type="submit" style="display: block; margin-left: 15%; margin-top: 2%;">Guardar</button>
                </form>
            </div>


            <div style="display: flex; align-items: center; margin-top: 1%; margin-left: 15%;">
                <form action="agregar_comentario.php" method="POST">
                    <input type="hidden" name="commentID" value="<?php echo $commentID; ?>">
                    <input type="hidden" name="archivo_id" value="<?php echo $archivo_id; ?>">
                    <!-- Muestra el contenido actual del comentario -->
                    <button type="submit" style="margin-left: 20%">Regresar</button>
                </form>
            </div>


            <!-- COMENTARIOS -->
            <div style="margin-top: 10%;">
                <h3 style="text-align: left; margin-left: 14%; margin-right: 20%;">Por favor, ten en cuenta que al editar el comentario, cualquier cambio realizado será visible para todos los usuarios. Asegúrate de revisar cuidadosamente tus modificaciones antes de guardar. Se recomienda evitar el uso de lenguaje inapropiado o contenido sensible.</h3>
                <h3 style="text-align: left; margin-left: 16%;">¡Gracias por contribuir a mantener un entorno respetuoso y seguro para todos!</h3>
            </div>
        </div>
    </div>

    <script>
        function limpiarTextarea() {
            document.getElementById("new_comment_content").value = "";
        }
    </script>
</body>
</html>
