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

// Verificar si la conexión se estableció correctamente
if ($conn === false) {
    echo "No se pudo establecer la conexión a la base de datos.";
    die(print_r(sqlsrv_errors(), true));
}

// Consulta SQL para obtener los archivos en espera
$consulta = "SELECT * FROM Files WHERE authorizationStatus = 'En espera'";
$resultado = sqlsrv_query($conn, $consulta);

// Consulta SQL para obtener los archivos en espera
$consulta_Acept = "SELECT * FROM Files WHERE authorizationStatus = 'aceptado'";
$resultado_Acept = sqlsrv_query($conn, $consulta_Acept);

// Consulta SQL para obtener los archivos en espera
$consulta_Rech = "SELECT * FROM Files WHERE authorizationStatus = 'rechazado'";
$resultado_Rech = sqlsrv_query($conn, $consulta_Rech);

// Verificar si la consulta se ejecutó correctamente
if ($resultado === false) {
    echo "Ha ocurrido un error al obtener los archivos en espera.";
    die(print_r(sqlsrv_errors(), true));
}

// Función para actualizar el estado del archivo a "Aceptado"
function actualizarEstado($conn, $fileId) {
    $sql = "UPDATE Files SET authorizationStatus = 'Aceptado' WHERE fileID = ?";
    $params = array($fileId);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        echo "Ha ocurrido un error al actualizar el estado del archivo.";
        die(print_r(sqlsrv_errors(), true));
    } else {
        // Retraso de 1 segundo antes de redirigir
        echo "<script>
                  setTimeout(function() {
                      window.location.href = 'ArchEspera.php';
                  }, 1000);
              </script>";
    }
}

// Función para actualizar el estado del archivo a "Rechazado"
function rechazarArchivo($conn, $fileId) {
    $sql = "UPDATE Files SET authorizationStatus = 'Rechazado' WHERE fileID = ?";
    $params = array($fileId);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        echo "Ha ocurrido un error al actualizar el estado del archivo.";
        die(print_r(sqlsrv_errors(), true));
    } else {
        // Retraso de 1 segundo antes de redirigir
        echo "<script>
                  setTimeout(function() {
                      window.location.href = 'ArchEspera.php';
                  }, 1000);
              </script>";
    }
}

// Función para obtener la información del estudiante por su userID
function obtenerInformacionEstudiante($conn, $userID) {
    $sql = "SELECT U.names, U.apellidoPaterno, U.apellidoMaterno, U.semester, C.carreerName FROM [User] U
            INNER JOIN Carreer C ON C.carreerID = U.carreerID 
            WHERE userID = ?";
    $params = array($userID);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        echo "Ha ocurrido un error al obtener la información del estudiante.";
        die(print_r(sqlsrv_errors(), true));
    }

    // Verificar si la consulta devolvió filas
    if (sqlsrv_has_rows($stmt)) {
        $estudiante = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        return $estudiante;
    } else {
        return false; // No se encontraron resultados
    }


}

// Verificar si se ha enviado el ID del archivo para actualizar o mostrar la información del estudiante
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["aceptar_file_id"])) { // Verifica si se presionó el botón de aceptar
        $fileIdToUpdate = $_POST["file_id"];
        actualizarEstado($conn, $fileIdToUpdate);
    } elseif (isset($_POST["rechazar_file_id"])) { // Verifica si se presionó el botón de rechazar
        $fileIdToReject = $_POST["file_id"];
        rechazarArchivo($conn, $fileIdToReject);
    } elseif (isset($_POST["info_user_id"])) {
        $userID = $_POST["info_user_id"];
        $estudiante = obtenerInformacionEstudiante($conn, $userID);
        if ($estudiante === false) {
            echo "No se encontró información del estudiante.";
        } else {
            // Mostrar la información del estudiante en una ventana modal
            echo "<div id='myModal' class='modal'>";
            echo "<div class='modal-content'>";
            echo "<span class='close'>&times;</span>";
            echo "<p class = 'infEstudiante'><strong>Nombre:</strong> {$estudiante['names']} {$estudiante['apellidoPaterno']} {$estudiante['apellidoMaterno']}</p>";
            echo "<p class = 'infEstudiante'><strong>Semestre:</strong> {$estudiante['semester']}</p>";
            echo "<p class = 'infEstudiante'><strong>Carrera:</strong> {$estudiante['carreerName']}</p>";
            echo "</div>";
            echo "</div>";
            echo "<script>
                    var modal = document.getElementById('myModal');
                    var span = document.getElementsByClassName('close')[0];
                    modal.style.display = 'block';
                    span.onclick = function() {
                        modal.style.display = 'none';
                    }
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = 'none';
                        }
                    }
                  </script>";
        }
    }
}

// Verificar si se ha enviado el ID del archivo para actualizar o mostrar la información del estudiante
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["aceptar_file_id"])) { // Verifica si se presionó el botón de aceptar
        // Código para actualizar el estado del archivo a "Aceptado"
    } elseif (isset($_POST["rechazar_file_id"])) { // Verifica si se presionó el botón de rechazar
        // Código para actualizar el estado del archivo a "Rechazado"
    } elseif (isset($_POST["info_user_id"])) {
        // Código para mostrar la información del estudiante en una ventana modal
    }
}

// Variables para controlar qué resultados mostrar
$mostrarEnEspera = true;
$mostrarAceptados = false;
$mostrarRechazados = false;

// Verificar si se ha presionado algún botón
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["en_espera"])) {
        $mostrarEnEspera = true;
        $mostrarAceptados = false;
        $mostrarRechazados = false;
    } elseif (isset($_POST["aceptados"])) {
        $mostrarEnEspera = false;
        $mostrarAceptados = true;
        $mostrarRechazados = false;
    } elseif (isset($_POST["rechazados"])) {
        $mostrarEnEspera = false;
        $mostrarAceptados = false;
        $mostrarRechazados = true;
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archivos en Espera</title>
    <link rel="stylesheet" href="ArchEspera.css">
    <link rel="icon" href="../images/logo.png" type="image/png">

</head>
<body>

<div class="rectangulo-izquierdo">

    <a href="" class="moderador-link">
        <img src="iconos/gerente.png" alt="Moderador" class="Moderador">
        <span class="titModerador">Moderador</span>
    </a>

    <a href="" class="validar-link">
        <img src="iconos/validar.png" alt="Validar" class="Validar">
        <span class="titValidar">Validar</span>
    </a>

    <a href="" class="Reporte-link">
        <img src="iconos/Reporte.png" alt="Reporte" class="Reporte">
        <span class="titReporte">Reportes</span>
    </a>

    <a href="" class="Categorias-link">
        <img src="iconos/categoria.png" alt="Categorias" class="Categorias">
        <span class="titCategorias">Categorias</span>
    </a>

    <a href="" class="Estudiante-link">
        <img src="iconos/crearmoderador.png" alt="Estudiante" class="Estudiante">
        <span class="titEstudiante">Estudiante</span>
    </a>

    <a href="../login.html" class="CerrarSesion-link">
        <img src="iconos/cerrar_sesion.png" alt="CerrarSesion" class="CerrarSesion">
        <span class="titCerrarSesion">Cerrar Sesión</span>
    </a>

</div>

<div class="rectangulo-superior">
    <h1 class="InterfazTit">Validar documento</h1>
    <a href="principalmoderador.html">
    <img src= "iconos/nexus.png" alt= "nexus" class= "nexus"></a>
    <div class = "Opciones">
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <button class = "b1" type="submit" name="en_espera">En espera</button>
            <button class = "b2" type="submit" name="aceptados">Aceptados</button>
            <button class = "b3" type="submit" name="rechazados">Rechazados</button>
        </form>
    </div>
</div>

<div class="container">
    <table>
        <thead>
            <tr>
                <th class="BarraTit" style="width: 8%;">Tipo</th>
                <th class="BarraTit" style="width: 200%;">Nombre del Archivo</th>
                <th class="BarraTit" style="width: 80%;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php

             // Dependiendo de las variables $mostrarEnEspera, $mostrarAceptados y $mostrarRechazados, mostrar los resultados correspondientes
             if ($mostrarEnEspera) {
                // Muestra los archivos en espera
                while ($fila = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>";
                    $fileType = strtolower($fila['fileType']);
                    $iconPath = "iconos/$fileType.png"; 
                    if (file_exists($iconPath)) {
                        echo "<a href='ver_archivo.php?id={$fila['fileID']}'><img class='icon' src='$iconPath' alt='Icono' width='32' height='32'></a>";
                    } else {
                        echo "<i class='fas fa-file'></i>"; 
                    }
                    echo "</td>";
                    echo "<td class='ArchName'>{$fila['fileName']}</td>";
                    echo "<td class='actions'>";
                    echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'><input type='hidden' name='file_id' value='{$fila['fileID']}'><button type='submit' name='aceptar_file_id'><img src='iconos/aceptar.png' alt='Aceptar' title='Aceptar' width='32' height='32' onclick='return confirmarAccion()'></button></form>";
                    echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'><input type='hidden' name='file_id' value='{$fila['fileID']}'><button type='submit' name='rechazar_file_id'><img src='iconos/rechazar.png' alt='Rechazar' title='Rechazar' width='32' height='32' onclick='return confirmarAccion()'></button></form>";
                    echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'><input type='hidden' name='info_user_id' value='{$fila['userID']}'><button type='submit'><img src='iconos/informacion.png' alt='informacion' title='información' width='32' height='32'></button></form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } elseif ($mostrarAceptados) {
                // Muestra los archivos aceptados
                while ($fila_Acept = sqlsrv_fetch_array($resultado_Acept, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>";
                    $fileType = strtolower($fila_Acept['fileType']);
                    $iconPath = "iconos/$fileType.png"; 
                    if (file_exists($iconPath)) {
                        echo "<a href='ver_archivo.php?id={$fila_Acept['fileID']}'><img class='icon' src='$iconPath' alt='Icono' width='32' height='32'></a>";
                    } else {
                        echo "<i class='fas fa-file'></i>"; 
                    }
                    echo "</td>";
                    echo "<td class='ArchName'>{$fila_Acept['fileName']}</td>";
                    echo "<td class='actions'>";
                    echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'><input type='hidden' name='file_id' value='{$fila_Acept['fileID']}'><button type='submit' name='rechazar_file_id'><img src='iconos/rechazar.png' alt='Rechazar' title='Rechazar' width='32' height='32' onclick='return confirmarAccion()'></button></form>";
                    echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'><input type='hidden' name='info_user_id' value='{$fila_Acept['userID']}'><button type='submit'><img src='iconos/informacion.png' alt='informacion' title='información' width='32' height='32'></button></form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } elseif ($mostrarRechazados) {
                // Muestra los archivos aceptados
                while ($fila_rech = sqlsrv_fetch_array($resultado_Rech, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>";
                    $fileType = strtolower($fila_rech['fileType']);
                    $iconPath = "iconos/$fileType.png"; 
                    if (file_exists($iconPath)) {
                        echo "<a href='ver_archivo.php?id={$fila_rech['fileID']}'><img class='icon' src='$iconPath' alt='Icono' width='32' height='32'></a>";
                    } else {
                        echo "<i class='fas fa-file'></i>"; 
                    }
                    echo "</td>";
                    echo "<td class='ArchName'>{$fila_rech['fileName']}</td>";
                    echo "<td class='actions'>";
                    echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'><input type='hidden' name='file_id' value='{$fila_rech['fileID']}'><button type='submit' name='aceptar_file_id'><img src='iconos/aceptar.png' alt='Aceptar' title='Aceptar' width='32' height='32' onclick='return confirmarAccion()'></button></form>";
                    echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'><input type='hidden' name='info_user_id' value='{$fila_rech['userID']}'><button type='submit'><img src='iconos/informacion.png' alt='informacion' title='información' width='32' height='32'></button></form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } 

            

            


            ?>
        </tbody>
    </table>
</div>

<script>
    function confirmarAccion() {
        return confirm('¿Estás seguro de que deseas realizar esta acción?');
    }
</script>

</body>
</html>

<?php
// Cierra la conexión a la base de datos
sqlsrv_close($conn);
?>
