<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha proporcionado el ID del comentario y del archivo
    if (isset($_POST['commentID']) && isset($_POST['archivo_id'])) {
        // Obtener el ID del comentario y del archivo
        $commentID = $_POST['commentID'];
        $archivo_id = $_POST['archivo_id'];

        // Realizar la conexión a la base de datos
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
        $conn = sqlsrv_connect($serverName, $connectionOptions);

        // Verificar la conexión
        if ($conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Obtener el ID del usuario actual basado en el email
        $email = $_SESSION['email'];
        $query = "SELECT userID FROM [User] WHERE email = ?";
        $params = array($email);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            echo "Error al obtener el ID del usuario.";
        } else {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if(isset($row['userID'])) {
                $userID = $row['userID'];

                // Verificar si el comentario pertenece al usuario actual
                $query = "SELECT * FROM Comments WHERE commentsID = ? AND userID = ?";
                $params = array($commentID, $userID);
                $stmt = sqlsrv_query($conn, $query, $params);

                if ($stmt === false) {
                    echo "Error al verificar el comentario.";
                } else {
                    $rows = sqlsrv_has_rows($stmt);
                    if ($rows === true) {
                        // El comentario pertenece al usuario actual, se puede eliminar
                        $query = "DELETE FROM Comments WHERE commentsID = ?";
                        $params = array($commentID);
                        $stmt = sqlsrv_query($conn, $query, $params);

                        if ($stmt === false) {
                            echo "Error al eliminar el comentario.";
                        } else {
                            echo "Comentario eliminado correctamente.";
                        }
                    } else {
                        echo "El comentario no pertenece al usuario actual.";
                    }
                }
            } else {
                echo "El ID del usuario no está definido.";
            }
        }

        // Cerrar la conexión a la base de datos
        sqlsrv_close($conn);
    } else {
        echo "No se proporcionó el ID del comentario o del archivo.";
    }
} else {
    echo "Acceso no permitido.";
}
?>
