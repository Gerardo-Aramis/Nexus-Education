<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["userID"]) && isset($_POST["CommentsID"]) && isset($_POST["sancion"])) {
    $userID = $_POST["userID"];
    $CommentsID = intval($_POST["CommentsID"]);
    $sancion = $_POST["sancion"];

    
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
        echo "No se estableció la conexión.";
        die(print_r(sqlsrv_errors(), true));
    }

    switch ($sancion) {
        case "1":
            $query = "DELETE FROM Comments WHERE CommentsID = ?";
            $params = array($CommentsID);
            break;
        case "3":
            $query = "UPDATE [User] SET Sanction = 3, fecha_sancion = GETDATE(), fin_sancion = DATEADD(day, 3, GETDATE()) WHERE userID = ?";
            $params = array($userID);
            break;
        case "6":
            $query = "UPDATE [User] SET Sanction = 6, fecha_sancion = GETDATE(), fin_sancion = DATEADD(day, 6, GETDATE()) WHERE userID = ?";
            $params = array($userID);
            break;
        case "permanentemente":
            $query = "UPDATE [User] SET Sanction = 10, fecha_sancion = GETDATE(),fin_sancion = default  WHERE userID = ?";
            $params = array($userID);
            break;
        case "Quitar":
            $query = "UPDATE [User] SET UserTypeID = 1, Sanction = null, fecha_sancion = null, fin_sancion = null WHERE userID = ?";
            $params = array($userID);
            break;
        default:
            echo "No se ha seleccionado una sanción válida.";
            exit;
    }

    $stmt = sqlsrv_query($conn, $query, $params);
    if ($stmt === false) {
        echo "Error al ejecutar la consulta.";
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($conn);
}
?>

<script>
    function toggleCarreraSelect() {
        var campoSelect = document.getElementById("campo");
        var carreraSelect = document.getElementById("carreraSelect");

        if (campoSelect.value === "carrera") {
            carreraSelect.disabled = false;
        } else {
            carreraSelect.disabled = true;
        }
    }

    function showModal(userID, CommentsID) {
        var modal = document.getElementById("myModal");
        var userIDInput = document.getElementById("userID");
        var commentsIDInput = document.getElementById("CommentsID");
        userIDInput.value = userID;
        commentsIDInput.value = CommentsID;
        
        modal.style.display = "block";
    }

    function eliminarComentario() {
        if (confirm('¿Estás seguro de eliminar este comentario?')) {
            var userID = document.getElementById("deleteUserID").value;
            var CommentsID = document.getElementById("deleteCommentsID").value;
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "");

            var hiddenUserIDField = document.createElement("input");
            hiddenUserIDField.setAttribute("type", "hidden");
            hiddenUserIDField.setAttribute("name", "userID");
            hiddenUserIDField.setAttribute("value", userID);
            form.appendChild(hiddenUserIDField);

            var hiddenCommentsIDField = document.createElement("input");
            hiddenCommentsIDField.setAttribute("type", "hidden");
            hiddenCommentsIDField.setAttribute("name", "CommentsID");
            hiddenCommentsIDField.setAttribute("value", CommentsID);
            form.appendChild(hiddenCommentsIDField);

            var sancionField = document.createElement("input");
            sancionField.setAttribute("type", "hidden");
            sancionField.setAttribute("name", "sancion");
            sancionField.setAttribute("value", "1"); // Valor para indicar eliminación de comentario
            form.appendChild(sancionField);

            document.body.appendChild(form);
            form.submit();
        }
    }

    function confirmacion(sancion) {
        if (confirm('¿Estás seguro de aplicar esta sanción?')) {
            var userID = document.getElementById("userID").value;
            var commentsID = document.getElementById("CommentsID").value;
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "");

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "userID");
            hiddenField.setAttribute("value", userID);
            form.appendChild(hiddenField);

            var commentsIDField = document.createElement("input");
            commentsIDField.setAttribute("type", "hidden");
            commentsIDField.setAttribute("name", "CommentsID");
            commentsIDField.setAttribute("value", commentsID);
            form.appendChild(commentsIDField);

            var sancionField = document.createElement("input");
            sancionField.setAttribute("type", "hidden");
            sancionField.setAttribute("name", "sancion");
            sancionField.setAttribute("value", sancion);
            form.appendChild(sancionField);

            document.body.appendChild(form);
            form.submit();
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        var span = document.getElementsByClassName("close")[0];

        span.onclick = function() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            var modal = document.getElementById("myModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sancionar comentarios</title>
    <link rel="stylesheet" href="report-style.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
    
</head>
<body>
<span class="menu-btn" onclick="openNav()">&#9776;</span>
<!-- Ventana modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Opciones de sanción</h2>
        <p>Selecciona la sanción que deseas aplicar.</p>
        <form id="sancines" method="post">
            <input type="hidden" name="userID" id="userID">
            <input type="hidden" name="CommentsID" id="CommentsID">
            <input type="hidden" name="sancion" id="sancion">
            <button type="button" onclick="eliminarComentario()">Eliminar comentario</button> <br>
            <button type="button" onclick="confirmacion('3')">Bloquear por 3 días</button> <br>
            <button type="button" onclick="confirmacion('6')">Bloquear por 6 días</button> <br>
            <button type="button" onclick="confirmacion('permanentemente')">Bloquear permanentemente</button> <br>
            <button type="button" onclick="confirmacion('Quitar')">Quitar sanción</button> <br>
        </form>
    </div>
</div>

<div class="top-section">
        <a href="../homepage.php" style="margin-top: 50px;">
        <img src="../images/nexus.png" alt="Logo Nexus" class="logo-nexus"></a>
        <h1 >Sancionar comentarios</h1>
</div>

<div class="left-section"> 
            
            <!-- SE SUSTITUYE EL HTML HACIA DONDE SE QUIERE REDIRECCIONAR --> 
            <a href="../estadisticas/principalmoderador.html" class="moderador-link">
            <img src="images/moderador.png" alt="moderador" class="fotosperfil" style="margin-left: -17px; margin-top: -15px; width: 50px; height: auto;"></a>
            <a href="../estadisticas/principalmoderador.html" class="moderador-link">
            <img src="images/textomoderador.png" alt="textomoderador" class="fotosperfil" style="margin-left: 30px; margin-top: -5px; width: 150px; height: auto;">
            </a>
            
            <img src="images/validar.png" alt="Validar" class="fotosperfil" style="margin-left: -17px; margin-top: 5px; width: 125px; height: auto;">
                <a href="validar.html" style="margin-left: 25px; margin-top: 80px;">Validar</a>
            <img src="images/reporte.png" alt="reporte" class="fotosperfil" style="margin-left: -10px; margin-top: 75px; width: 130px; height: auto; ">
                <a href="reporte.html" style="margin-left: 25px; margin-top: 70px;">Reporte</a>
            <img src="images/categoria.png" alt="categoria" class="fotosperfil" style="margin-left: -12px; margin-top: 175px; width: 140px; height: auto;">
                <a href="organizarcontenido.html" style="margin-left: 25px; margin-top: 65px;">Categorias</a>
            <img src="images/crearmoderador.png" alt="Validar" class="fotosperfil" style="margin-left: 10px; margin-top: 320px; width: 55px; height: auto;">
                <a href="../students/opcionesestudiantes.html" style="margin-left: 25px; margin-top: 70px;">Estudiantes</a>
            <img src="images/Cerrar_sesion.png" alt="Cerrar_sesion" class="fotosperfil" style="margin-top: 430px;  "> 
                <a href="iniciarsesion.html" style="margin-left: 7px; margin-top: 80px;">Cerrar sesión</a>
        </div>
        

<div class  = "RectSup">
<div class  = "SubRect">

    <img class = "Logo" src="../images/logo.png" alt="Logo">
    <h2 class= "SubTit">Buscar comentarios</h2>
    <form action="" method="get" id="searchForm">
        <label class= "bus" for="campo">Buscar por:</label>
        <select id="campo" name="campo" onchange="toggleCarreraSelect()">
            <option value="nombre">Nombre de usuario</option>
            <option value="carrera">Carrera</option>
            <option value="noCtrol">No. Control</option>
        </select>
        <select id="carreraSelect" name="carrera" disabled>
            <option value="">Seleccione una carrera</option>
            <?php
            // Obtener la lista de carreras desde la base de datos
            $serverName = "25.41.90.44\\SQLEXPRESS"; 
            $connectionOptions = array(
                "Database" => "NexusEducation",
                "UID" => "log_userweb", 
                "PWD" => "nexus123", 
                "CharacterSet" => "UTF-8"
            );


            $conn = sqlsrv_connect($serverName, $connectionOptions);
            if ($conn === false) {
                echo "No se estableció la conexión.";
                die (print_r(sqlsrv_errors(), true));
            }

            $query_carreras = "SELECT * FROM Carreer";
            $result_carreras = sqlsrv_query($conn, $query_carreras);
            if ($result_carreras === false) {
                echo "Error al obtener las carreras.";
                die (print_r(sqlsrv_errors(), true));
            }
            while ($row_carrera = sqlsrv_fetch_array($result_carreras, SQLSRV_FETCH_ASSOC)) {
                echo "<option value='" . $row_carrera['carreerID'] . "'>" . $row_carrera['carreerName'] . "</option>";
            }
            ?>
        </select>
        <input type="text" id="valor" name="valor">
        <button type="submit">Buscar</button>
    </form>
    </div>
</div>
<?php
$serverName = "25.41.90.44\\SQLEXPRESS"; 
$connectionOptions = array(
    "Database" => "NexusEducation",
    "UID" => "log_userweb", 
    "PWD" => "nexus123", 
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "No se estableció la conexión.";
    die (print_r(sqlsrv_errors(), true));
}

// Verificar si se envió el formulario de búsqueda
if (isset($_GET['campo']) && isset($_GET['valor'])) {
    $campo = $_GET['campo'];
    $valor = $_GET['valor'];
    if ($campo === 'nombre') {
        // Si la búsqueda es por nombre, apellidos o carrera, se filtra por las columnas correspondientes
        $query = "SELECT U.userID, CO.CommentsID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, CO.commentContent, CO.reportStatus, U.Sanction, U.fecha_sancion, U.fin_sancion 
        FROM [User] U
        INNER JOIN Carreer C ON U.carreerID = C.carreerID
        LEFT JOIN Comments CO ON U.userID = CO.userID
        WHERE (U.names LIKE '%$valor%' OR CONCAT(U.names, ' ', U.apellidoPaterno, ' ', U.apellidoMaterno) LIKE '%$valor%' OR U.apellidoPaterno LIKE '%$valor%' OR U.apellidoMaterno 
        LIKE '%$valor%') and CO.reportStatus IS NOT NULL";
    } elseif ($campo === 'carrera') {
        // Si la búsqueda es por carrera, se utiliza el ID de la carrera para filtrar
        $valor = $_GET['carrera']; // Se obtiene el ID de carrera del formulario
        $query = "SELECT U.userID, CO.CommentsID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, CO.commentContent, CO.reportStatus, U.Sanction, U.fecha_sancion, U.fin_sancion
        FROM [User] U
        INNER JOIN Carreer C ON U.carreerID = C.carreerID
        LEFT JOIN Comments CO ON U.userID = CO.userID
        WHERE U.carreerID = '$valor' and CO.reportStatus IS NOT NULL";
    } else {
        // En caso contrario, se filtra por el campo seleccionado
        $query = "SELECT U.userID, CO.CommentsID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, CO.commentContent, CO.reportStatus, U.Sanction, U.fecha_sancion, U.fin_sancion 
        FROM [User] U
        INNER JOIN Carreer C ON U.carreerID = C.carreerID
        LEFT JOIN Comments CO ON U.userID = CO.userID
        WHERE $campo LIKE '%$valor%' and CO.reportStatus IS NOT NULL";
    }
} else {
    // En caso contrario, se filtra por el campo seleccionado
    if (!empty($campo)) {
        $query = "SELECT U.userID, CO.CommentsID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, CO.commentContent,CO.reportStatus, U.Sanction, U.fecha_sancion, U.fin_sancion 
        FROM [User] U
        INNER JOIN Carreer C ON U.carreerID = C.carreerID
        LEFT JOIN Comments CO ON U.userID = CO.userID
        WHERE $campo LIKE '%$valor%' and CO.reportStatus IS NOT NULL";
    } else {
        // Si $campo está vacío, asigna una condición que siempre sea verdadera para evitar errores de sintaxis
        $query = "SELECT U.userID, CO.CommentsID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, CO.commentContent, CO.reportStatus, U.Sanction, U.fecha_sancion, U.fin_sancion 
        FROM [User] U
        INNER JOIN Carreer C ON U.carreerID = C.carreerID
        LEFT JOIN Comments CO ON U.userID = CO.userID
        WHERE 1=1 and CO.reportStatus IS NOT NULL";
    }
}
$result = sqlsrv_query($conn, $query);
if ($result === false) {
    echo "Error al ejecutar la consulta.";
    die (print_r(sqlsrv_errors(), true));
}
?>

<?php if(isset($_GET['campo']) && isset($_GET['valor'])): ?>
    <a href="SanComentarios.php">Mostrar Todos los Usuarios</a>
<?php endif; ?>

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

    <a href="../estadisticas/principalmoderador.html" class="moderador-link">
        <img src="../validar/iconos/gerente.png" alt="Moderador" class="Moderador">
        <span class="titModerador">Moderador</span>
    </a>
    <a href="" class="validar-link">
        <img src="../validar/iconos/validar.png" alt="Validar" class="Validar">
        <span class="titValidar">Validar</span>
    </a>

    <a href="" class="Reporte-link">
        <img src="../validar/iconos/Reporte.png" alt="Reporte" class="Reporte">
        <span class="titReporte">Reportes</span>
    </a>

    <a href="" class="Categorias-link">
        <img src="../validar/iconos/categoria.png" alt="Categorias" class="Categorias">
        <span class="titCategorias">Categorias</span>
    </a>

    <a href="" class="Estudiante-link">
        <img src="../validar/iconos/crearmoderador.png" alt="Estudiante" class="Estudiante">
        <span class="titEstudiante">Estudiante</span>
    </a>

    <a href="../login.html" class="CerrarSesion-link">
        <img src="../validar/iconos/cerrar_sesion.png" alt="CerrarSesion" class="CerrarSesion">
        <span class="titCerrarSesion">Cerrar Sesión</span>
    </a>
</div>

<div class = "Tbl">
    <h2 class='lis'>Lista de comentarios reportados:</h2>
    <table class='table'>
        <tr>
            <th>No. Control</th>
            <th>Nombre completo</th>
            <th>Comentario</th>
            <th>Razon del reporte</th>
            <th>Tipo de sanción</th>
            <th>Fecha de sanción</th>
            <th>Fin de sanción</th>
            <th style='text-align: center;'>Opciones</th>
        </tr>
        <?php
        // Iterar sobre los resultados y mostrar cada usuario en una fila de la tabla
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['noCtrol'] . "</td>";
            echo "<td>" . $row['names'] . " " . $row['apellidoPaterno'] . " " . $row['apellidoMaterno'] . "</td>";
            echo "<td>" . $row['commentContent'] . "</td>";
            echo "<td>" . $row['reportStatus'] . "</td>";
            echo "<td>" . $row['Sanction'] . "</td>";
            echo "<td>";
            if ($row['fecha_sancion'] !== null) {
                echo $row['fecha_sancion']->format('Y-m-d');
            }
            echo "<td>";           
            if ($row['fin_sancion'] !== null) {
                echo $row['fin_sancion']->format('Y-m-d');
            }
            echo "</td>";           
            echo "<td style='text-align: center;'>";
            echo "<button onclick='showModal(" . $row['userID'] . "," . $row['CommentsID'] . ")'>...</button>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<script>
    function toggleCarreraSelect() {
        var campoSelect = document.getElementById("campo");
        var carreraSelect = document.getElementById("carreraSelect");

        if (campoSelect.value === "carrera") {
            carreraSelect.disabled = false;
        } else {
            carreraSelect.disabled = true;
        }
    }

    function showModal(userID, CommentsID) {
        var modal = document.getElementById("myModal");
        document.getElementById("userID").value = userID;
        document.getElementById("CommentsID").value = CommentsID;
        modal.style.display = "block";
    }

    function eliminarComentario() {
        if (confirm('¿Estás seguro de eliminar este comentario?')) {
            var form = document.getElementById("sancines");
            document.getElementById("sancion").value = "1"; // Valor para eliminar comentario
            form.submit();
        }
    }

    function confirmacion(sancion) {
        if (confirm('¿Estás seguro de aplicar esta sanción?')) {
            var form = document.getElementById("sancines");
            document.getElementById("sancion").value = sancion;
            form.submit();
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            document.getElementById("myModal").style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == document.getElementById("myModal")) {
                document.getElementById("myModal").style.display = "none";
            }
        }
    });
</script>

<script>
    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
    }

    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
    }
</script>
</body>
</html>