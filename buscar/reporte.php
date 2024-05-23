<?php
session_start();

// Verificar si se ha proporcionado el ID del archivo
if (isset($_POST['archivo_id'])) {
    // Obtener el ID del archivo
    $archivo_id = $_POST['archivo_id'];

    // Verificar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificar si se ha proporcionado el comentario
        if (isset($_POST['comentario'])) {
            // Obtener el contenido del comentario
            $comentario = $_POST['comentario'];

            // Insertar el comentario en la base de datos
            $serverName = "25.41.90.44\\SQLEXPRESS"; 
            $connectionOptions = array(
                "Database" => "NexusEducation",
                "UID" => "log_userweb", 
                "PWD" => "nexus123", 
                "CharacterSet" => "UTF-8"
            );
$conn = sqlsrv_connect($serverName, $connectionOptions);

            // Obtener el ID del usuario actual basado en el email
            $email = $_SESSION['email'];
            $query = "SELECT userID FROM [User] WHERE email = ?";
            $params = array($email);
            $stmt = sqlsrv_query($conn, $query, $params);

            if ($stmt === false) {
                // Manejar el error
            } else {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                if(isset($row['userID'])) {
                    $userID = $row['userID'];

                    // Insertar el comentario en la tabla Comments
                    $query = "INSERT INTO Comments (commentContent, commentDate, userID, fileID, authorizationID) VALUES (?, GETDATE(), ?, ?, ?)";
                    $params = array($comentario, $userID, $archivo_id, 1); // Aquí asumí que el authorizationID es 1, debes ajustarlo según tus necesidades
                    $stmt = sqlsrv_query($conn, $query, $params);

                    if ($stmt === false) {
                        // Manejar el error
                    } else {
                        // Comentario insertado correctamente
                    }
                } else {
                    // Manejar el error
                }
            }
        } else {
            // No se proporcionó el comentario
        }
    }
} else {
    // No se proporcionó el ID del archivo
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Education</title>
    <link rel="stylesheet" href="estilospantallacomentario.css">
    <link rel="icon" href="images/nexus2.png" type="image/x-icon">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



    <style>
        button {
            width: 100px; /* Ancho del botón */
            height: 40px; /* Altura del botón */
            font-size: 14px; /* Ajusta el tamaño de la fuente del botón */
            margin-top: 2%;
            margin-left: 5%;
        }
        
        .button-container {
    display: flex;
    gap: 10px; /* Espacio entre los botones */
}
.button-container form {
    margin: 0; /* Eliminar cualquier margen */
}


        textarea {
            height: 40px; /* Altura del textarea */
            width: 300px; /* Ancho del textarea */
            margin-top: 10px; /* Margen superior */
            padding: 10px; /* Relleno dentro del textarea */
            font-family: Arial, Helvetica, sans-serif; /* Familia de fuentes */
            font-size: 16px; /* Tamaño de la fuente */
            border: 1px solid #ccc; /* Borde del textarea */
            border-radius: 5px; /* Radio de los bordes del textarea */
            resize: vertical; /* Permitir que el usuario redimensione verticalmente */
            box-sizing: border-box; /* Incluir el relleno y el borde en el tamaño total */
        }

        /* Estilo para los ComboBox */
        select {
            border: none;
            border-radius: 50px;
            padding: 5px 10px;
            height: 40px;
            width: 60%;
            text-align: left; /* Alinea el texto a la izquierda */
            font-family: Arial, sans-serif; /* Cambiar la fuente */
            font-size: 100%; /* Cambiar el tamaño de la letra */
            margin-bottom: 0%;
            align-items: center; 
            margin-top: 1%;

        }

        /* Estilos para las opciones del combobox */
        option {
            font-family: Arial, sans-serif; /* Cambiar la fuente */
            font-size: 16px; /* Cambiar el tamaño del texto */
            color: #575656; /* Cambiar el color del texto */
            /* Otros estilos según tus preferencias */
        }

        /* Estilos para el combobox en estado de enfoque */
        select:focus {
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

    </style>
</head>
<body>

    <div class="container">
        <div class="top-section">
            <img src="images/tecnm.png" alt="Logo Tecnm" class="logo-tecnm">
            <img src="images/ittepic.png" alt="Logo Tec Tepic" class="logo-ittepic">
            <a href="../homepage.php">
                <img src="images/nexus2.png" alt="Logo Nexus" class="logo-nexus">
            </a>
            <img src="images/nombrenexus.png" alt="Logo nombre Nexus" class="logo-nombrenexus">
        </div>
        
        <div class="bottom-section">
            
            <div class="center-section" style="background-color: #2A677C">
                
            <!--Mostrar el formulario para agregar comentario -->
            <form action='agregar_comentario.php' method='POST'  style="display: flex; flex-direction: row;">
                <input type='hidden' name='archivo_id' value='<?php echo $archivo_id; ?>'>
                <h3 for='comentario' style="margin-left: -52%; display: flex; margin-right: 2%;">Añadir comentario</h3>
                <textarea  name='comentario' id='comentario' class='comment-form' required></textarea>
                <div class='button-container'>
                    <button type='submit' style="background-image: url('images/agregarcomentario.png'); background-size: 65% auto; background-repeat: no-repeat; background-position: center; border-radius: 40px;  width: 45px; height: 45px;"></button>
                </div>
            </form>
                
            <!-- Comentarios en forma de lista -->
            <div id="comentarios">
                <!-- Aquí se agregarán los comentarios -->
                <?php
                // Obtener comentarios de la base de datos si se proporcionó el ID del archivo
                $serverName = "25.41.90.44\\SQLEXPRESS"; 
                $connectionOptions = array(
                    "Database" => "NexusEducation",
                    "UID" => "log_userweb", 
                    "PWD" => "nexus123", 
                    "CharacterSet" => "UTF-8"
                );
$conn = sqlsrv_connect($serverName, $connectionOptions);
                    if ($conn === false) {
                        die (print_r(sqlsrv_errors(), true));
                    }

                    // Obtener el ID del usuario actual basado en el email
                    $email = $_SESSION['email'];
                    $query = "SELECT userID FROM [User] WHERE email = ?";
                    $params = array($email);
                    $stmt = sqlsrv_query($conn, $query, $params);

                    if ($stmt === false) {
                        // Manejar el error
                    } else {
                        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                        if(isset($row['userID'])) {
                            $userID = $row['userID'];

                            $query = "SELECT C.commentsID, C.commentContent, C.commentDate, C.userID, U.names, U.apellidoPaterno, U.apellidoMaterno FROM Comments C JOIN [User] U ON C.userID = U.userID WHERE C.fileID = ?";
                            $params = array($archivo_id);
                            $stmt = sqlsrv_query($conn, $query, $params);

                            if ($stmt === false) {
                                // Manejar el error
                            } else {
                                echo "<h3>Comentarios relacionados con el archivo:</h3>";
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    echo "<p><strong>" . $row['names'] . " " . $row['apellidoPaterno'] . " " . $row['apellidoMaterno']  . "</strong> &nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;";
                                    echo "<strong>" . $row['commentDate']->format('Y-m-d H:i:s') . "</strong></p>";
                                    echo "<p>" . $row['commentContent'] . "</p>";

                                    // Verificar si la clave 'userID' está definida en $row
                                    if (isset($row['userID'])) {
                                        // Mostrar el botón de editar solo si el comentario pertenece al usuario actual
                                        if ($userID == $row['userID']) {
                                            $commentsId = $row['commentsID'];
                                            echo "<div class='button-container'>";
                                            echo "<form action='editar_comentario.php' method='POST'>";
                                            echo "<input type='hidden' name='archivo_id' value='" . $archivo_id . "'>";
                                            echo "<input type='hidden' name='commentID' value='$commentsId'>";
                                            echo "<button type='submit'>Editar</button>";
                                            echo "</form>";

                                            echo "<form id='eliminar-comentario-form-$commentsId' class='eliminar-comentario-form' action='' method='POST'>";
                                            echo "<input type='hidden' name='archivo_id' value='$archivo_id'>";
                                            echo "<input type='hidden' name='commentID' value='$commentsId'>";
                                            echo "<button type='button' onclick='eliminarComentario($commentsId)'>Eliminar</button>";
                                            echo "</form>";
                                            echo "</div>";
                                        }else {


                                            // El usuario puede reportar este comentario
                                            /*
                                            $comentarios_del_usuario = true;
                                            echo "<p><strong>Usuario:</strong> " . $row['names'] . " " . $row['apellidoPaterno'] . " " . $row['apellidoMaterno'] . "</p>";
                                            echo "<p><strong>Fecha:</strong> " . $row['commentDate']->format('Y-m-d H:i:s') . "</p>";
                                            echo "<p><strong>Comentario:</strong> " . $row['commentContent'] . "</p>";
                                            */
                                            // Mostrar formulario de reporte para el comentario
                                            echo "<form action='reporte.php' method='POST'>";
                                            echo "<input type='hidden' name='archivo_id' value='$archivo_id'>";
                                            echo "<input type='hidden' name='comment_id' value='".$row['commentsID']."'>";
                                            echo "<input type='hidden' name='comment_userID' value='".$row['userID']."'>";
                                            echo "<select name='report_reason'>";
                                            echo "<option value='Spam'>Spam</option>";
                                            echo "<option value='Amenazas o Violencia'>Amenazas o Violencia</option>";
                                            echo "<option value='Información falsa o engañosa'>Información falsa o engañosa</option>";
                                            echo "<option value='Comentario Inapropiado'>Comentario Inapropiado</option>";
                                            echo "<option value='Link malicioso'>Link malicioso</option>";
                                            echo "</select>";
                                            echo "<button type='submmit' name='report_comment' onclick='reportarComentario(".$row['commentsID'].")'>Reportar</button>";
                                            

                                          
                                            echo "</form>";
                                        }
                                    } else {

                        
                                    }
                                }
                            }
                        }
                    }
                
                ?>
            </div>
        </div> 
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
    
</div>

<!-- Script para eliminar comentarios -->
<script>
    function eliminarComentario(commentId) {
        if (confirm("¿Estás seguro de que quieres eliminar este comentario?")) {
            var formId = "eliminar-comentario-form-" + commentId;
            var formData = new FormData(document.getElementById(formId));

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "eliminar_comentario.php", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Actualizar la vista si es necesario
                    location.reload(); // Esto recargará la página después de eliminar el comentario
                }
            };
            xhr.send(formData);
        }
    }
</script>

</body>
<script>

        // Aquí puedes agregar el código JavaScript necesario para reportar el comentario sin enviar el formulario
        // Por ejemplo, podrías mostrar un mensaje de confirmación o abrir un modal para que el usuario confirme el reporte
        // Luego, podrías enviar una solicitud AJAX al servidor para reportar el comentario
        // Recuerda adaptar este código según tus necesidades específicas
        $(document).ready(function() {
    $('#report-comment-button').click(function() {
        // Obtener los datos del formulario
        var formData = $('#report-comment-form').serialize();
        
        // Realizar la solicitud AJAX
        $.ajax({
            type: 'POST',
            url: 'reporte.php',
            data: formData,
            success: function(response) {
                // Manejar la respuesta del servidor
                
            },
            error: function(xhr, status, error) {
                // Manejar errores de la solicitud AJAX
                alert('Error al procesar la solicitud: ' + error);
            }
        });
    });
});
    
</script>

</html>


<?php
session_start();
// Definir una variable para almacenar el mensaje de alerta
// Verificar si se ha proporcionado el ID del archivo
$alert_message = "";

if (isset($_POST['archivo_id'])) {
    // Obtener el ID del archivo
    $archivo_id = $_POST['archivo_id'];

    // Verificar si se ha enviado el formulario de reporte
    if (isset($_POST['report_comment'])) {
        // Verificar si se ha proporcionado una razón de reporte
        if (isset($_POST['report_reason'])) {
            // Obtener la razón de reporte seleccionada
            $report_reason = $_POST['report_reason'];

            // Verificar si el usuario no está reportando su propio comentario
            if (isset($row['userID']) != $_POST['comment_userID']) {
                // Cambiar el estado de la autorización del archivo asociado al comentario
                
                $serverName = "IA-27";
                $connectionOptions = array(
                "Database" => "NexusEducation",
                "UID" => "sa",
                "PWD" => "20SQL22"
            );
$conn = sqlsrv_connect($serverName, $connectionOptions);

                if ($conn === false) {
                    echo "No se estableció la conexión.";
                    die (print_r(sqlsrv_errors(), true));
                }

                // Actualizar el estado de la autorización del archivo
             $query = "UPDATE Comments SET reportStatus = ? WHERE CommentsID = ?";

                $params = array($report_reason, $_POST['comment_id']);
                $stmt = sqlsrv_query($conn, $query, $params);

                if ($stmt === false) {
                    echo "Error al actualizar el estado de la autorización del archivo: " . print_r(sqlsrv_errors(), true);
                } else {
                   
                    $alert_message =  "El comentario ha sido reportado exitosamente.";
                }
            } else {
                echo "No puedes reportar tu propio comentario.";
            }
        } else {
            echo "Por favor, seleccione una razón de reporte.";
        }
    }

    // Mostrar comentarios y formulario de reporte
    // Código para mostrar comentarios y formulario de reporte aquí...
} else {
    echo "No se proporcionó el ID del archivo.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reporte</title>
</head>
<body>

<?php
// Si se definió un mensaje de alerta, lo mostramos en JavaScript
if (!empty($alert_message)) {
    echo "<script>alert('$alert_message');</script>";

}
?>

<!-- Aquí va el resto de tu formulario HTML --></form>

</body>
</html>