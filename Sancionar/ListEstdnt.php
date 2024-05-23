<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["userID"]) && isset($_POST["sancion"])) {
    $userID = $_POST["userID"];
    $sancion = $_POST["sancion"];

    // Verificar si la sanción es por 3 días
    if ($sancion === "3") {
        // Realizar la conexión a la base de datos
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

        // Ejecutar la consulta SQL para actualizar la sanción del usuario
        $query = "UPDATE [User] SET Sanction = 3, fecha_sancion = GETDATE(), fin_sancion = DATEADD(day, 3, GETDATE()) WHERE userID = ?";
        $params = array($userID);
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt === false) {
            echo "Error al ejecutar la consulta.";
            die (print_r(sqlsrv_errors(), true));
        }        

        // Cerrar la conexión
        sqlsrv_close($conn);

    } elseif($sancion === "6"){
         // Realizar la conexión a la base de datos
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
 
         // Ejecutar la consulta SQL para actualizar la sanción del usuario
         $query = "UPDATE [User] SET Sanction = 6, fecha_sancion = GETDATE(), fin_sancion = DATEADD(day, 6, GETDATE()) WHERE userID = ?";
         $params = array($userID);
         $stmt = sqlsrv_query($conn, $query, $params);
         if ($stmt === false) {
             echo "Error al ejecutar la consulta.";
             die (print_r(sqlsrv_errors(), true));
         } 
 
         // Cerrar la conexión
         sqlsrv_close($conn);
    }elseif($sancion === "permanentemente"){
        // Realizar la conexión a la base de datos
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

        // Ejecutar la consulta SQL para actualizar la sanción del usuario
        $query = "UPDATE [User] SET Sanction = 10, fecha_sancion = GETDATE(), UserTypeID = 1002 WHERE userID = ?";
        $params = array($userID);
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt === false) {
            echo "Error al ejecutar la consulta.";
            die (print_r(sqlsrv_errors(), true));
        } 

        // Cerrar la conexión
        sqlsrv_close($conn);
    }elseif($sancion === "Quitar"){
       // Realizar la conexión a la base de datos
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

       // Ejecutar la consulta SQL para actualizar la sanción del usuario
       $query = "UPDATE [User] SET UserTypeID = 1, Sanction = null, fecha_sancion = null, fin_sancion = null WHERE userID = ?";
       $params = array($userID);
       $stmt = sqlsrv_query($conn, $query, $params);
       if ($stmt === false) {
           echo "Error al ejecutar la consulta.";
           die (print_r(sqlsrv_errors(), true));
       } 

       // Cerrar la conexión
       sqlsrv_close($conn); 
    }else {
        echo "No se ha seleccionado una sanción válida.";
    }
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

    function showModal(userID) {
        var modal = document.getElementById("myModal");
        var userIDInput = document.getElementById("userID");
        userIDInput.value = userID; // Establecer el ID de usuario en el campo oculto
        modal.style.display = "block";
    }

    function confirmacion(sancion) {
        if (confirm('¿Estás seguro de aplicar esta sanción?')) {
            var userID = document.getElementById("userID").value;
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "");

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "userID");
            hiddenField.setAttribute("value", userID);
            form.appendChild(hiddenField);

            var sancionField = document.createElement("input");
            sancionField.setAttribute("type", "hidden");
            sancionField.setAttribute("name", "sancion");
            sancionField.setAttribute("value", sancion);
            form.appendChild(sancionField);

            document.body.appendChild(form);
            form.submit();
        }
    }
    
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
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="Lista.css">
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
        <form action="" method="post">
            <input type="hidden" name="userID" id="userID">
            <button type="button" onclick="confirmacion('3')">Bloquear por 3 días</button> <br>
            <button type="button" onclick="confirmacion('6')">Bloquear por 6 días</button> <br>
            <button type="button" onclick="confirmacion('permanentemente')">Bloquear permanentemente</button> <br>
            <button type="button" onclick="confirmacion('Quitar')">Quitar sanción</button> <br>
        </form>

    </div>
</div>

<div class  = "RectSup">
<div class  = "SubRect">
    <h2 class= "Tit">Sancionar usuario</h2>
    <img class = "Logo" src="../images/logo.png" alt="Logo">
    <h2>Buscar Usuario</h2>
    <form action="" method="get" id="searchForm">
        <label for="campo">Buscar por:</label>
        <select id="campo" name="campo" onchange="toggleCarreraSelect()">
            <option value="nombre">Nombre</option>
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
        $query = "SELECT U.userID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, U.email, C.carreerName, U.semester, U.Sanction, U.fecha_sancion, U.fin_sancion 
        FROM [User] U
        INNER JOIN Carreer C ON U.carreerID = C.carreerID
        WHERE (U.names LIKE '%$valor%' OR CONCAT(U.names, ' ', U.apellidoPaterno, ' ', U.apellidoMaterno) LIKE '%$valor%' OR U.apellidoPaterno LIKE '%$valor%' OR U.apellidoMaterno LIKE '%$valor%') and U.UserTypeID != 2";
    } elseif ($campo === 'carrera') {
        // Si la búsqueda es por carrera, se utiliza el ID de la carrera para filtrar
        $valor = $_GET['carrera']; // Se obtiene el ID de carrera del formulario
        $query = "SELECT U.userID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, U.email, C.carreerName, U.semester, U.Sanction, U.fecha_sancion, U.fin_sancion
        FROM [User] U
        INNER JOIN Carreer C ON U.carreerID = C.carreerID
        WHERE U.carreerID = '$valor' and U.UserTypeID != 2";
    } else {
        // En caso contrario, se filtra por el campo seleccionado
        $query = "SELECT U.userID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, U.email, C.carreerName, U.semester, U.Sanction, U.fecha_sancion, U.fin_sancion 
        FROM [User] U
        INNER JOIN Carreer C ON U.carreerID = C.carreerID
        WHERE $campo LIKE '%$valor%' and U.UserTypeID != 2";
    }
} else {
    // Query para obtener todos los usuarios
    $query = "SELECT U.userID, U.noCtrol, U.names, U.apellidoPaterno, U.apellidoMaterno, U.email, C.carreerName, U.semester, U.Sanction, U.fecha_sancion, U.fin_sancion 
    FROM [User] U
    INNER JOIN Carreer C ON U.carreerID = C.carreerID
    WHERE U.UserTypeID != 2";
}

$result = sqlsrv_query($conn, $query);
if ($result === false) {
    echo "Error al ejecutar la consulta.";
    die (print_r(sqlsrv_errors(), true));
}
?>

<?php if(isset($_GET['campo']) && isset($_GET['valor'])): ?>
    <a href="ListEstdnt.php">Mostrar Todos los Usuarios</a>
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
    <h2>Lista de Usuarios</h2>
    <table>
        <tr>
            <th>No. Control</th>
            <th>Nombre completo</th>
            <th>Email</th>
            <th>Carrera</th>
            <th>Semestre</th>
            <th>Tipo de sanción</th>
            <th>Fecha de sanción</th>
            <th>Fin de sanción</th>
            <th>Sancionar</th>
        </tr>
        <?php
        // Iterar sobre los resultados y mostrar cada usuario en una fila de la tabla
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['noCtrol'] . "</td>";
            echo "<td>" . $row['names'] . " " . $row['apellidoPaterno'] . " " . $row['apellidoMaterno'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['carreerName'] . "</td>";
            echo "<td>" . $row['semester'] . "</td>";
            echo "<td>" . $row['Sanction'] . "</td>";
            echo "<td>";
            if ($row['fecha_sancion'] !== null) {
                echo $row['fecha_sancion']->format('Y-m-d');
            }
            echo "<td>";           
            if ($row['fin_sancion'] !== null) {
                echo $row['fin_sancion']->format('Y-m-d');
            }
            echo "<td><button onclick='showModal(" . $row['userID'] . ")'>Sancionar</button></td>";
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

    function showModal(userID) {
        var modal = document.getElementById("myModal");
        var userIDInput = document.getElementById("userID");
        userIDInput.value = userID; // Establecer el ID de usuario en el campo oculto
        modal.style.display = "block";
    }
    
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
