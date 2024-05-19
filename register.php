<?php
$serverName = "25.41.90.44\\SQLEXPRESS"; 
$connectionOptions = array(
    "Database" => "NexusEducation",
    "UID" => "log_userweb", 
    "PWD" => "nexus123", 
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if( $conn === false ) {
    echo "No se estableció la conexión. ";
    die(print_r(sqlsrv_errors(), true));
} else {
    echo "Conectado";
}

$names = $_POST['nombre'];
$apellidoPaterno = $_POST['ApellidoPaterno'];
$apellidoMaterno = $_POST['ApellidoMaterno'];
$sex = $_POST['Sexo'];
$noCtrol = $_POST['NoControl'];
$email = $_POST['email'];
$passwordd = $_POST['password'];
$carrera = $_POST['Carrera'];
$semester = $_POST['Semestre'];

if ($sex == 1){
    $sex = 'MASCULINO';
} elseif ($sex == 2){
    $sex = 'FEMENINO';
} else {
    $sex = 'NO BINARIO';
}

// Preparar la consulta SQL con parámetros
$consulta = "INSERT INTO [User] VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)";
$params = array($names, $apellidoPaterno, $apellidoMaterno, $sex, $noCtrol, $email, $passwordd, $semester, $carrera);

// Preparar y ejecutar la consulta
$resultado = sqlsrv_query($conn, $consulta, $params);
if($resultado === false){
    echo "Ha ocurrido un error al registrar sus datos.";
    die(print_r(sqlsrv_errors(), true));
} else {
    // Redireccionar al usuario después de la inserción
    header("Location: login.html");
}

// Consulta para obtener el UserID del usuario recién insertado
$consultaUserId = "SELECT TOP 1 UserID FROM [User] ORDER BY UserID DESC";
$resultadoUserId = sqlsrv_query($conn, $consultaUserId);

if ($resultadoUserId === false) {
    echo "Ha ocurrido un error al obtener el UserID del usuario recién insertado.";
    die(print_r(sqlsrv_errors(), true));
}

$userId = sqlsrv_fetch_array($resultadoUserId, SQLSRV_FETCH_ASSOC)['UserID'];

// Consulta para insertar la foto predeterminada en la tabla PictureProfile
$consultaFoto = "INSERT INTO PictureProfile (UserID, pictureProfile) VALUES ($userId, 'images/usuario.png')";
$resultadoFoto = sqlsrv_query($conn, $consultaFoto);

if ($resultadoFoto === false) {
    echo "Ha ocurrido un error al insertar la foto predeterminada en la tabla PictureProfile.";
    die(print_r(sqlsrv_errors(), true));
} else {
    header("Location: login.html");
}

sqlsrv_close($conn);
?>
