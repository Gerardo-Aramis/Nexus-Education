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
}

$email = $_POST['email'];
$new_password = $_POST['new_password'];
$confirm_new_password = $_POST['confirm_new_password'];

#echo "$username $new_password $confirm_new_password";

$consulta = "UPDATE [User] SET passwordd = '$new_password' WHERE email = '$email'";
$resultado = sqlsrv_prepare($conn, $consulta);

sqlsrv_execute($resultado);

if(!sqlsrv_execute($resultado)){
  echo"Ha ocurrido un error al registrar sus datos." ;
}else {
  header("Location: login.html"); #cambiar a la de iniciar sesión
  exit;
  #echo "<h2>La contraseña ha sido actualizada. </h2>";
}

sqlsrv_close($conn);

?>
