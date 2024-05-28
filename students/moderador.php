<?php
    
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
    die("No se estableció la conexión: " . print_r(sqlsrv_errors(), true));
}

// Función para actualizar el tipo de usuario a Moderador
function actualizarTipoUsuario($noControl, $conn) {
    $consulta = "UPDATE [User] SET userTypeId = '2' WHERE noCtrol = ?";

    $params = array($noControl);
    $resultado = sqlsrv_query($conn, $consulta, $params);

    if ($resultado === false) {
        return false;
    }

    return true;
}

// Función para obtener los datos del usuario
function obtenerDatosUsuario($noControl, $conn) {
    $consulta = "SELECT u.names, u.apellidoPaterno, u.apellidoMaterno,
        c.carreerName,
        u.semester,
        u.email,
        u.userID,
        u.noCtrol
        FROM [User] u
        INNER JOIN Carreer c ON u.carreerID = c.carreerID
        WHERE u.noCtrol = ?";

    $params = array($noControl);
    $resultado = sqlsrv_query($conn, $consulta, $params);

    if ($resultado === false || sqlsrv_has_rows($resultado) === false) {
        return false;
    }

    return sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC);
}


// Procesar el formulario si se envió por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['noControl'])) {
        $control = $_POST['noControl'];

        $datosUsuario = obtenerDatosUsuario($control, $conn);

        if ($datosUsuario === false) {
            header("Location: moderador.php");
        }
    }

    // Si se confirma la acción de crear un nuevo moderador
    if (isset($_POST['userId']) && isset($_POST['confirmacion']) && $_POST['confirmacion'] === 'confirmado') {
        $noControl = $_POST['userId'];
        if (actualizarTipoUsuario($noControl, $conn)) {
            header("Location: moderador.php");
        } else {
            header("Location: moderador.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Education</title>
    <link rel="stylesheet" href="estilospantallasmoderador.css">
    <link href="../images/logo.png" rel="shortcut icon">

    <style>
         
    </style>
</head>
<body>

<div class="container">
        
    <div class="top-section">
        <a href="../estadisticas/principalmoderador.html" style="margin-top: 50px;">
        <img src="../images/nexus.png" alt="Logo Nexus" class="logo-nexus"></a>
        <h1 >Crear moderador</h1>
    </div>

    <div class="bottom-section">
        <form action="#" method="POST">
            <div style="display: flex; align-items: center;">
                <h3 style="margin-right: -80px; color:rgb(255, 255, 255); margin-bottom: 45px; padding-top: 10px;">Número de control</h3>
                <input style="width: 300px;" type="text" id="noControl" name="noControl" value="<?php echo isset($datosUsuario['noCtrol']) ? $datosUsuario['noCtrol'] : ''; ?>" placeholder="Buscar" required <?php echo isset($datosUsuario) ? 'readonly' : ''; ?>>
                <button style="font-size: 20px;width: 30px;height: 30px; margin: 100px; margin-top: -285px; margin-left: 280px; background-image: url('images/buscar.png'); background-size: cover; background-repeat: no-repeat; border: none; background-color: transparent; border-radius: 0;"></button>
            </div>
        </form>

        <?php if(isset($datosUsuario) && is_array($datosUsuario)): ?>
    <div style="display: flex; align-items: center; margin-top: 35px; margin-left: -180px;">
        <h3 style="margin-left: -20px; color:rgb(255, 255, 255); margin-bottom: 10px; padding-top: 0px; margin-top: -10px;">Nombre del usuario</h3>
        <input style="margin-left: 20px;" type="text" id="usuario" name="usuario" value="<?php echo $datosUsuario['names'] . ' ' . $datosUsuario['apellidoPaterno'] . ' ' . $datosUsuario['apellidoMaterno']; ?>" readonly>
    </div>

    <div style="display: flex; align-items: center; margin-top: 20px; margin-left: -270px;">
        <h3 style="margin-left: 70px; color:rgb(255, 255, 255); margin-bottom: 10px; padding-top: 25px; margin-top: 20px;">Correo electrónico</h3>
        <input style="margin-left: 30px;  margin-top: -15px;" type="text" id="email" name="email" value="<?php echo $datosUsuario['email']; ?>" readonly>
    </div>

    <div style="display: flex; align-items: center; margin-bottom: 75px;  margin-left: -200px;">
        <h3 style="margin-right: 5px;  color:rgb(255, 255, 255); margin-top: 2px;">Semestre</h3>
        <span id="semestre" style=" margin-left:15px ; width: 70px; padding: 10px; margin-top: -20px;border: 1px solid #ccc; border-radius: 30px; margin-right: 15px;"><?php echo $datosUsuario['semester']; ?></span>
        <h3 style="margin-right: -80px;  color:rgb(255, 255, 255); margin-top: 0px;">Carrera</h3>
        <input style= "margin-top: -15px; width:390px;" type="text" id="carrera" name="carrera" value="<?php echo $datosUsuario['carreerName']; ?>" readonly>
    </div>

    <!-- Botón de crear moderador con confirmación -->
    <form action="#" method="POST">
        <input type="hidden" name="userId" value="<?php echo $datosUsuario['noCtrol']; ?>">
        <input type="hidden" name="confirmacion" value="confirmado">
        <button type="submit" style="font-size: 20px;width: 250px;height:45px; margin-top: 20px; margin-left: -180px;" onclick="return confirm('¿Estás seguro de que deseas crear un nuevo moderador para este usuario?')">Crear moderador</button>
    </form>
<?php elseif(isset($errorMessage)): ?>
    <p><?php echo $errorMessage; ?></p>
<?php endif; ?>


        <div style="top:10px; bottom:20px;">
        <?php if(isset($datosUsuario)): ?>
            <a href="moderador.php" style="color:white; font-size: 20px;  margin-top: -700px; margin-left: 210px;">Buscar otro usuario</a>
        
            <?php endif; ?>
            </div>
    </div>
    
    <div class="left-section"> 
            <!-- SE SUSTITUYE EL HTML HACIA DONDE SE QUIERE REDIRECCIONAR -->
            <img src="../images/moderador.png" alt="moderador" class="fotosperfil" style="margin-left: -17px; margin-top: -15px; width: 50px; height: auto;">
            <img src="../images/textomoderador.png" alt="textomoderador" class="fotosperfil" style="margin-left: 30px; margin-top: -5px; width: 150px; height: auto;">

            <img src="../images/validar.png" alt="Validar" class="fotosperfil" style="margin-left: -17px; margin-top: 5px; width: 125px; height: auto;">
                <a href="../validar/ArchEspera.php" style="margin-left: 25px; margin-top: 80px;">Validar</a>
            <img src="../images/reporte.png" alt="reporte" class="fotosperfil" style="margin-left: -10px; margin-top: 75px; width: 130px; height: auto; ">
                <a href="../report/SanComentarios.php" style="margin-left: 25px; margin-top: 70px;">Reporte</a>
            <img src="../images/categoria.png" alt="categoria" class="fotosperfil" style="margin-left: -12px; margin-top: 175px; width: 140px; height: auto;">
                <a href="../categoria/organizarcontenido.html" style="margin-left: 25px; margin-top: 65px;">Categorias</a>
            <img src="../images/crearmoderador.png" alt="Validar" class="fotosperfil" style="margin-left: 10px; margin-top: 320px; width: 55px; height: auto;">
                <a href="opcionesestudiantes.html" style="margin-left: 25px; margin-top: 70px;">Estudiantes</a>
            <img src="../images/Cerrar_sesion.png" alt="Cerrar_sesion" class="fotosperfil" style="margin-top: 430px;  "> 
                <a href="../index.html" style="margin-left: 7px; margin-top: 80px;">Cerrar sesión</a>

        </div>
    
</div>
</body>
</html>
