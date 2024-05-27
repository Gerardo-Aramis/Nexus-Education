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

$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    echo "No se estableció la conexión. ";
    die (print_r(sqlsrv_errors(), true));
} else {
    echo "Conectado";
}

$email = $_POST['user'];
$password = $_POST['password'];

// Consulta SQL para obtener el hash de la contraseña
$consulta = "SELECT passwordd FROM [User] WHERE email = ?";
$resultado = sqlsrv_prepare($conn, $consulta, array(&$email));

if ($resultado === false) {
    echo "Ha ocurrido un error al consultar los datos.";
    die (print_r(sqlsrv_errors(), true));
}

if (!sqlsrv_execute($resultado)) {
    echo "Ha ocurrido un error al consultar los datos.";
    die (print_r(sqlsrv_errors(), true));
}

// Verificar si se encontraron resultados
if (sqlsrv_has_rows($resultado)) {
    $fila = sqlsrv_fetch_array($resultado);
    $password_hash = $fila['passwordd'];

    // Verificar la contraseña hasheada
    if (password_verify($password, $password_hash)) {
        // Iniciar sesión
        session_start();
        $_SESSION['email'] = $email;

        // Obtener el tipo de usuario y la fecha de sanción
        $consulta_usuario = "SELECT userTypeID, fin_sancion FROM [User] WHERE email = ?";
        $resultado_usuario = sqlsrv_prepare($conn, $consulta_usuario, array(&$email));

        if (!sqlsrv_execute($resultado_usuario)) {
            echo "Ha ocurrido un error al consultar los datos.";
            die (print_r(sqlsrv_errors(), true));
        }

        $fila_usuario = sqlsrv_fetch_array($resultado_usuario);
        $typeUser = $fila_usuario['userTypeID'];
        $fecSanc = $fila_usuario['fin_sancion'];

        // Obtener la fecha actual
        $fechaActual = new DateTime();

        // Verificar el tipo de usuario y la fecha de sanción
        if ($typeUser == 1 && $fechaActual <  $fecSanc) {
            header("Location: Sancionar/PagSan.html");
            exit();
        } elseif ($typeUser == 1 &&  $fecSanc  < $fechaActual) {
            header("Location: homepage.php");
            exit();
        } elseif ($typeUser == 2) {
            header("Location: login.html?Est-Mod=true");
            exit;
        } else {
            header("Location: Sancionar/PagSan.html");
            exit();
        }
    } else {
        // Contraseña incorrecta
        echo "<script>alert('Contraseña incorrecta');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }
} else {
    // Usuario no encontrado
    echo "<script>alert('Usuario no encontrado');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

sqlsrv_close($conn);
?>