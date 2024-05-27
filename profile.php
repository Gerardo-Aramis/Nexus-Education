<?php
session_start();

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
if ($conn === false) {
    echo "No se estableció la conexión.";
    die (print_r(sqlsrv_errors(), true));
}

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Consulta SQL para obtener los datos del usuario
    $consulta = "SELECT u.UserID, u.names, u.apellidoPaterno, u.apellidoMaterno,
    c.carreerName,
    u.semester,
    u.email,
    P.pictureProfile
    FROM [User] u
    INNER JOIN Carreer c ON u.carreerID = c.carreerID
    LEFT JOIN PictureProfile P ON P.userID = u.userID
    WHERE u.email = ?";

    $resultado = sqlsrv_prepare($conn, $consulta, array(&$email));

    if ($resultado === false) {
        echo "Ha ocurrido un error al consultar los datos.";
        die (print_r(sqlsrv_errors(), true));
    }

    if (!sqlsrv_execute($resultado)) {
        echo "Ha ocurrido un error al consultar los datos.";
        die (print_r(sqlsrv_errors(), true));
    }

    // Obtén los datos del usuario si hay filas en el resultado
    if (sqlsrv_has_rows($resultado)) {
        // Fetch del primer (y único) resultado
        $datosUsuario = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC);
    } else {
        echo "No se encontraron datos para el usuario con el correo electrónico proporcionado.";
    }
} else {
    echo "No se ha iniciado sesión.";
}

if (isset($_SESSION['email'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['editarSemestre'])&& !empty($_POST['nuevoSemestre'])) {
            // Aquí va el código para procesar el formulario
            $eml = $_SESSION['email']; // Obtén el correo electrónico del formulario
            $sem = $_POST['nuevoSemestre'];

            $consulta = "UPDATE [User] SET semester = ? WHERE email = ?";
            $resultado = sqlsrv_prepare($conn, $consulta, array(&$sem, &$eml)); 

            if ($resultado === false) {
                echo "Ha ocurrido un error al preparar la consulta.";
                die (print_r(sqlsrv_errors(), true));
            }

            if (!sqlsrv_execute($resultado)) {
                echo "Ha ocurrido un error al ejecutar la consulta.";
                die (print_r(sqlsrv_errors(), true));
            }

            // Redirige al usuario de nuevo a profile.php solo si la actualización se realizó correctamente
            header("Location: profile.php");//Buscar codigo para refrescar pagina y pegarlo aqui
            exit();
        } elseif (isset($_POST['editarCarrera']) && !empty($_POST['nuevaCarrera'])) {
            // Aquí va el código para procesar el formulario
            $eml = $_SESSION['email']; // Obtén el correo electrónico del formulario
            $car = $_POST['nuevaCarrera'];

            $consulta = "UPDATE [User] SET carreerID = ? WHERE email = ?";
            $resultado = sqlsrv_prepare($conn, $consulta, array(&$car, &$eml)); 

            if ($resultado === false) {
                echo "Ha ocurrido un error al preparar la consulta.";
                die (print_r(sqlsrv_errors(), true));
            }

            if (!sqlsrv_execute($resultado)) {
                echo "Ha ocurrido un error al ejecutar la consulta.";
                die (print_r(sqlsrv_errors(), true));
            }

            // Redirige al usuario de nuevo a profile.php solo si la actualización se realizó correctamente
            header("Location: profile.php");//Buscar codigo para refrescar pagina y pegarlo aqui
            exit();
        }
    }
}

if (isset($_SESSION['email'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fotoPerfil"])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["fotoPerfil"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["fotoPerfil"]["tmp_name"], $targetFilePath)) {
                // Verifica si ya hay una foto asociada al usuario
                if (!empty($datosUsuario['pictureProfile'])) {
                    // Si hay una foto, actualiza la ruta de la imagen en la tabla PictureProfile
                    $consultaImagen = "UPDATE PictureProfile SET pictureProfile = ? WHERE UserID = ?";
                    $resultadoImagen = sqlsrv_prepare($conn, $consultaImagen, array(&$targetFilePath, &$datosUsuario['UserID']));
                } else {
                    // Si no hay una foto, inserta una nueva fila en la tabla PictureProfile
                    $consultaImagen = "INSERT INTO PictureProfile (UserID, pictureProfile) VALUES (?, ?)";
                    $resultadoImagen = sqlsrv_prepare($conn, $consultaImagen, array(&$datosUsuario['UserID'], &$targetFilePath));
                }
                
                if ($resultadoImagen === false) {
                    echo "Ha ocurrido un error al preparar la consulta de la imagen.";
                    die (print_r(sqlsrv_errors(), true));
                }

                if (!sqlsrv_execute($resultadoImagen)) {
                    echo "Ha ocurrido un error al ejecutar la consulta de la imagen.";
                    die (print_r(sqlsrv_errors(), true));
                }

                // Redirige al usuario de nuevo a profile.php solo si la actualización se realizó correctamente
                header("Location: profile.php");
                exit();
            } else {
                echo "Ha ocurrido un error al subir la foto de perfil.";
            }
        } else {
            echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        }
    }
}

sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="../images/logo.png" rel="shortcut icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body,.container{
            background: #2A677C ;
            color: white;
        }

        .container{
            background: #2A677C;
            border: black;
            position: relative;
        }

        .rectangulo-superior {
            background-color: #202D35; /* Color de fondo */
            height: 100px; /* Altura del rectángulo */
            width: 100%; /* Ancho del rectángulo */
            position: fixed; /* Posición fija para que esté siempre en la parte superior */
            top: 0; /* Distancia desde la parte superior */
            left: 0; /* Distancia desde la izquierda */
            z-index: 999; /* Capa más alta para que esté por encima de otros elementos */
        }

        .container-derecha {
            float: right;
            margin-right: 5px; /* Ajusta este valor según sea necesario */
            text-align: left; 
            margin-top: 50px;
            width: 80%;
        }

        .foto{
            position: absolute;
            top: 22%;
            right: 36%;
        }

        .msjFoto{
            position: absolute;
            top: 22%;
            right: 36%;
        }

        .btnEleg{
            position: absolute;
            top: 58%;
            right: 17%; 
            border-radius:30px; 
        }

        #editarFoto{
            position: absolute;
            top: 150%;
            right: 73%;
            background: #65A7D3;
            border: unset;
            border-radius: 30px;
        }

        #editarFoto:hover {
            background-color: #0127AD; /* Cambiar el color de fondo al pasar el puntero */
        }

        .nom, .cor, .sem, .car, .msjFoto{
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: bold;
        }

        .rectangulo-izquierdo {
            background-color: #0E3C54; /* Color de fondo del rectángulo */
            width: 200px; /* Ancho del rectángulo */
            height: 100%; /* Altura del rectángulo, que ocupa toda la altura de la ventana */
            position: fixed; /* Permite que el rectángulo permanezca fijo en la ventana */
            top: 0; /* Coloca el rectángulo al principio de la ventana */
            left: 0; /* Coloca el rectángulo en el lado izquierdo de la ventana */
            z-index: 999; /* Capa más alta para que esté por encima de otros elementos */
        }

        .btn-editar {
            width: 25px;
            height: 25px;
            cursor: pointer;
            border-radius: 5px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-editar:hover {
            background-color: #e0e0e0; /* Cambiar el color de fondo al pasar el puntero */
        }

        .perfil-img {
            width: 100px; /* Ancho fijo deseado */
            height: 125px; /* Altura automática para mantener la proporción */
            object-fit: cover;
            border-radius:15px;
            border: 1px solid black;
        }

        .retrn{
            position: absolute;
            color: white;
            font-weight: bold;
            top: 110%;
            right: 60%;
        }

        .retrn:hover {
            color: #202D35; /* Cambia el color del enlace cuando el puntero está sobre él */
        }

        .misArchivos{
            width: 40px;
            height: 40px;
            position: absolute;
            top: 59%;
            right: 120%;
            z-index: 9999;
        }

        .mis_archivos{
            position: absolute;
            top: 61%;
            right: 110%;
            z-index: 9999;
            color: white;
            font-size: 17px;
            white-space: nowrap;/*Para que no se haga un salto de linea */
        }

        .mis_archivos:hover{
            color: white;
        }

        .subir{
            width: 45px;
            height: 45px;
            position: absolute;
            top: 41%;/*archivos 18*/
            right: 120%;
            z-index: 9999;
        }

        .Subir{
            position: absolute;
            top: 43%;
            right: 115%;
            z-index: 9999;
            color: white;
            font-size: 17px;
            white-space: nowrap;/*Para que no se haga un salto de linea */
        }

        .Subir:hover{
            color: white;
        }

        .Perfil{
            width: 45px;
            height: 45px;
            position: absolute;
            top: 25%;/*Subir 16*/
            right: 120%;
            z-index: 9999;
        }

        .perfil{
            position: absolute;
            top: 27%;
            right: 113%;
            z-index: 9999;
            color: white;
            font-size: 17px;
            white-space: nowrap;/*Para que no se haga un salto de linea */
        }

        .perfil:hover{
            color: white;
        }

        .cerrarSesion{
            width: 45px;
            height: 45px;
            position: absolute;
            top: 105%;/*Subir 16*/
            right: 120%;
            z-index: 9999;
        }

        .CerrarSesion{
            position: absolute;
            top: 107%;
            right: 110%;
            z-index: 9999;
            color: white;
            font-size: 17px;
            white-space: nowrap;/*Para que no se haga un salto de linea */
        }

        .CerrarSesion:hover{
            color: white;
        }

        .nexus{
            width: 97px;
            height: 97px;
            position: absolute;
            top: -12%;
            right: 112%;
            z-index: 9999;
        }

        .Titulo{
            position: absolute;
            top: -5%;
            right: 60%;
            z-index: 9999;
            color: white;
            font-size: 25px;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="rectangulo-izquierdo"></div>
    <div class="rectangulo-superior"></div>
    <div class="container container-derecha">
        <h1></h1>
        <br>
        <br>
        <br>
        <?php
        if (isset($datosUsuario)) {
            echo "<p class  = 'nom'>Nombre: </p>";
            echo "<p>" . $datosUsuario['names'] . " " . $datosUsuario['apellidoPaterno'] . " " . $datosUsuario['apellidoMaterno'] . "</p>";
            echo "<p class  = 'cor'>Correo electronico: </p>";
            echo "<p><span id='email'>" . $datosUsuario['email'] . "</span></p>";
            echo "<p class  = 'sem'>Semestre: </p>";
            echo "<p><span id='semestre'>" . $datosUsuario['semester'] . "</span> <img src='images/editar-codigo.png' alt='Editar Semestre' title= 'Editar semestre' class='btn-editar' data-toggle='modal' data-target='#modalEditarSemestre'></p>";
            echo "<p class  = 'car'>Carrera: </p>";
            echo "<p><span id='carrera'>" . $datosUsuario['carreerName'] . "</span> <img src='images/editar-codigo.png' alt='Editar Carrera' title = 'Editar carrera' class='btn-editar' data-toggle='modal' data-target='#modalEditarCarrera'></p>";
            // Verifica si hay una foto de perfil
            if (!empty($datosUsuario['pictureProfile'])) {
                echo "<p class='foto'><br> <img src='{$datosUsuario['pictureProfile']}' alt='Foto de perfil' class='perfil-img'></p>";
            } else {
                echo "<p class='foto'>Sin foto de perfil</p>";
            }
            echo "<p class = 'msjFoto'>Foto de perfil</p>";
            echo "<p><form action='profile.php' method='post' enctype='multipart/form-data' class = 'btnEleg'><input type='file' name='fotoPerfil'><button type='submit' class='btn btn-primary' id='editarFoto' title = 'Subir foto' >Subir</button></form></p>";
            echo "<img src='images/Mis_archivos.png' alt='Mis_archivos' class= 'misArchivos'>";
                echo "<a href='' class = 'mis_archivos' >Mis archivos</a>";
            echo "<img src='images/Subir.png' alt='subir' class= 'subir'>";
                echo "<a href='subir/SubirArchivo.html' class = 'Subir' >Subir</a>";
            echo "<img src='images/Mi_Perfil.png' alt='Perfil' class= 'Perfil'>";
                echo "<a href='' class = 'perfil' >Mi perfil</a>";
            echo "<img src='images/Cerrar_sesion.png' alt='cerrarSesion' class= 'cerrarSesion'>";
                echo "<a href='login.html' class = 'CerrarSesion' >Cerrar sesión</a>";
            echo "<img src='images/nexus.png' alt='nexus' class= 'nexus'>";
            echo "<p class = 'Titulo'>Mi perfil</p>";
            echo "<a href='homepage.php' class = 'retrn'>Volver</a></p>";

            // Agrega más campos según sea necesario
        }
        ?>
    </div>
   
     <!-- Ventana modal semestre-->
     <div class="modal fade" id="modalEditarSemestre" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Semestre</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="profile.php" method="post">
                        <div class="form-group">
                            <label for="nuevoSemestre">Nuevo Semestre:</label>
                            <!--<input type="text" class="form-control" id="nuevoSemestre" name="nuevoSemestre" placeholder="Ingrese el nuevo semestre">-->
                            <p>
                            <select type="hidden" class = "form-control" name="nuevoSemestre" id="nuevoSemestre">
                                <option value="1">Semestre 1</option>
                                <option value="2">Semestre 2</option>
                                <option value="3">Semestre 3</option>
                                <option value="4">Semestre 4</option>
                                <option value="5">Semestre 5</option>
                                <option value="6">Semestre 6</option>
                                <option value="7">Semestre 7</option>
                                <option value="8">Semestre 8</option>
                                <option value="9">Semestre 9</option>
                                <option value="10">Semestre 10</option>
                                <option value="11">Semestre 11</option>
                                <option value="12">Semestre 12</option>
                            </select>
                            </p>
                            <!-- Agrega un campo oculto para enviar el correo electrónico -->
                            <input type="hidden" name="editarSemestre" value="">
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventana modal para editar carrera -->
    <div class="modal fade" id="modalEditarCarrera" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Carrera</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="profile.php" method="post">
                        <div class="form-group">
                            <label for="nuevaCarrera">Nueva Carrera:</label>
                            <h5 class="modal-title" id="exampleModalLabel">Editar Carrera</h5>
                            <p>
                            <select type="hidden" class = "form-control" name="nuevaCarrera" id="nuevaCarrera">
                                <option value="1">Arquitectura</option>
                                <option value="2">Ingeniería Bioquímica</option>
                                <option value="3">Ingeniería Civil</option>
                                <option value="4">Ingeniería Eléctrica</option>
                                <option value="5">Ingeniería en Gestión Empresarial</option>
                                <option value="6">Ingeniería en Sistemas Computacionales</option>
                                <option value="7">Ingeniería Industrial</option>
                                <option value="8">Ingeniería Mecatrónica</option>
                                <option value="9">Ingeniería Química</option>
                                <option value="10">Licenciatura en Administración</option>
                            </select>
                            </p>
                            <input type="hidden" name="editarCarrera">
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Agrega un evento de clic al botón de editar semestre
        document.getElementById('editarSemestre').addEventListener('click', function() {
            // Muestra la ventana modal al hacer clic en el botón de editar semestre
            $('#modalEditarSemestre').modal('show');
        });

        // Agrega un evento de clic al botón de editar carrera
        document.getElementById('editarCarrera').addEventListener('click', function() {
            // Muestra la ventana modal al hacer clic en el botón de editar carrera
            $('#modalEditarCarrera').modal('show');
        });
    </script>

</body>
</html>
