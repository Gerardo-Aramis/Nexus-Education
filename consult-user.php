<?php
$serverName = "25.41.90.44\\SQLEXPRESS"; 
$connectionInfo = array(
    "Database" => "NexusEducation",
    "UID" => "log_userweb", 
    "PWD" => "nexus123", 
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    echo "No se estableció la conexión. ";
    die (print_r(sqlsrv_errors(), true));
} else {
    echo "Conectado";
}

$email = $_POST['user'];
$passwordd = $_POST['password'];

// Consulta SQL para verificar el usuario
$consulta = "SELECT * FROM [User] WHERE email = ? AND passwordd = ?";
$resultado = sqlsrv_prepare($conn, $consulta, array(&$email, &$passwordd));

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

    // Redirigir de manera segura si el usuario existe
    session_start();
    $_SESSION['email'] = $email;

    // Obtener el tipo de usuario y la fecha de sanción
    $fila = sqlsrv_fetch_array($resultado);
    $typeUser = $fila['userTypeID'];
    $fecSanc = $fila['fin_sancion'];


    // Obtener la fecha actual
    $fechaActual = new DateTime();

    // Verificar si la fecha de sanción está establecida y es posterior a la fecha actual
    if ($typeUser == 1 && $fechaActual <  $fecSanc) {
        header("Location: Sancionar/PagSan.html");
        exit();
    } elseif ($typeUser == 1 &&  $fecSanc  < $fechaActual) {
        header("Location: homepage.php");
        exit();
    } elseif ($typeUser == 2) {
        header("Location: estadisticas/principalmoderador.html");
        exit();
    } else {
        header("Location: Sancionar/PagSan.html");
        exit();
    }
} 

else {
    echo "<script>";
    echo "window.history.back();"; // Regresa a la página anterior sin recargar
    echo "</script>";
}

sqlsrv_close($conn);
?>
