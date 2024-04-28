<?php
$serverName = "IA-27";

$connectionInfo = array(
    "Database"=> "NexusEducation",
    "UID"=> "sa",
    "PWD"=> "20SQL22"
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
    header("Location: homepage.html");
    exit();
} else {
    echo "<script>";
    echo "window.history.back();"; // Regresa a la página anterior sin recargar
    echo "</script>";
}

sqlsrv_close($conn);
?>
