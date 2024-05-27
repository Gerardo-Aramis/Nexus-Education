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

$alertMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $names = htmlspecialchars(trim($_POST['nombre']));
    $apellidoPaterno = htmlspecialchars(trim($_POST['ApellidoPaterno']));
    $apellidoMaterno = htmlspecialchars(trim($_POST['ApellidoMaterno']));
    $sex = htmlspecialchars(trim($_POST['Sexo']));
    $noCtrol = htmlspecialchars(trim($_POST['NoControl']));
    $email = htmlspecialchars(trim($_POST['email']));
    $passwordd = password_hash(htmlspecialchars(trim($_POST['password'])), PASSWORD_BCRYPT);
    $carrera = htmlspecialchars(trim($_POST['Carrera']));
    $semester = htmlspecialchars(trim($_POST['Semestre']));

    if ($sex == 1) {
        $sex = 'MASCULINO';
    } elseif ($sex == 2) {
        $sex = 'FEMENINO';
    } else {
        $sex = 'NO BINARIO';
    }

    // Verificar si el usuario ya está registrado por su número de control
    $consultaVerificacionNoCtrol = "SELECT COUNT(*) as count FROM [User] WHERE noCtrol = ?";
    $paramsVerificacionNoCtrol = array($noCtrol);
    $resultadoVerificacionNoCtrol = sqlsrv_query($conn, $consultaVerificacionNoCtrol, $paramsVerificacionNoCtrol);
    if ($resultadoVerificacionNoCtrol === false) {
        $alertMessage = "Ha ocurrido un error al verificar el número de control.";
    } else {
        $filaVerificacionNoCtrol = sqlsrv_fetch_array($resultadoVerificacionNoCtrol, SQLSRV_FETCH_ASSOC);
        if ($filaVerificacionNoCtrol['count'] > 0) {
            $alertMessage = "El número de control ya está registrado.";
        } else {
            // Verificar si el usuario ya está registrado por su correo electrónico
            $consultaVerificacionEmail = "SELECT COUNT(*) as count FROM [User] WHERE email = ?";
            $paramsVerificacionEmail = array($email);
            $resultadoVerificacionEmail = sqlsrv_query($conn, $consultaVerificacionEmail, $paramsVerificacionEmail);
            if ($resultadoVerificacionEmail === false) {
                $alertMessage = "Ha ocurrido un error al verificar el correo electrónico.";
            } else {
                $filaVerificacionEmail = sqlsrv_fetch_array($resultadoVerificacionEmail, SQLSRV_FETCH_ASSOC);
                if ($filaVerificacionEmail['count'] > 0) {
                    $alertMessage = "El correo electrónico ya está registrado.";
                } else {
                    // Insertar el nuevo usuario en la base de datos
                    $consulta = "INSERT INTO [User] (names, apellidoPaterno, apellidoMaterno, sex, noCtrol, email, passwordd, semester, userTypeID, carreerID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)";
                    $params = array($names, $apellidoPaterno, $apellidoMaterno, $sex, $noCtrol, $email, $passwordd, $semester, $carrera);
                    $resultado = sqlsrv_query($conn, $consulta, $params);
                    if ($resultado === false) {
                        $alertMessage = "Ha ocurrido un error al registrar sus datos.";
                    } else {
                        $alertMessage = "Usuario registrado exitosamente.";

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
                    }
                }
            }
        }
    }

    // Mostrar el mensaje de alerta
    echo "<script>alert('$alertMessage');</script>";
}


sqlsrv_close($conn);
?>

<script>
    setTimeout(function() {
        window.location.href = "register.html"; // Redireccionar después de 3 segundos
    }, 200); // 1000 milisegundos = 1 segundos
</script>