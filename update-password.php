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
if( $conn === false ) {
  echo "No se estableció la conexión. ";
  die(print_r(sqlsrv_errors(), true));
}

$email = $_POST['email'];
#$new_password = $_POST['new_password'];
$new_password = password_hash(htmlspecialchars(trim($_POST['new_password'])), PASSWORD_BCRYPT);
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
