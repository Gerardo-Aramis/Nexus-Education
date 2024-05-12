<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Mis Archivos - Nexus Education</title>
  <link rel="stylesheet" href="style-my-files.css">
  <link href="images/logo.png" rel="shortcut icon">

  <!-- Aquí va el código CSS para el modal -->
  <style>
/* Estilos para la ventana modal */
.modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);            
        }

        .modal-content {
            background-color: #202D35;
            border: 2px solid #202D35; /* Cambia el color del borde a rojo (#ff0000) */
            width: 30%; /* Ancho deseado de la ventana modal */
            max-width: 800px; /* Ancho máximo permitido */
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 5px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-button {
    background-color: #202D35 !important;
    color: white !important;
    border: none !important;
    /* Otros estilos necesarios con !important */
}

  </style>

</head>
<body>
    <div class="container">
 
<!-- PARTE DE ARRIBA - TITULOS -->
    <div class="top-section">
        <a href="../homepage.php">
            <img src="../images/nexus.png" alt="Logo Nexus" class="logo-nexus">
        </a>
        <h1 > Mis archivos </h1>

    </div>

<!-- BARRA DE LA IZQUIERDA -->

    <div class="left-section" > 
        <!-- SE SUSTITUYE EL HTML HACIA DONDE SE QUIERE REDIRECCIONAR -->
        <div>
            <img src="../images/Mi_Perfil.png" alt="Mi_perfil" class="fotosperfil">
            <a href="../profile.php">Mi perfil</a>
        </div>
        <div>
            <img style="margin-top: 30%;" src="../images/Subir.png" alt="Subir" class="fotosperfil ">
            <a href="../subir/SubirArchivo.html">Subir</a>
        </div>
        <div>
            <img style="margin-top: 60%;" src="../images/Mis_archivos.png" alt="Mis_archivos" class="fotosperfil">
            <a href="my-files.php">Mis Archivos</a>
        </div>
        <div>
            <img style="margin-top: 85%;" src="../images/buscar.png" alt="Mis_archivos" class="fotosperfil">
            <a href="buscar.html">Buscar</a>
        </div>
        <div>
            <img style="margin-top: 200%;" src="../images/Cerrar_sesion.png" alt="Cerrar_sesion" class="fotosperfil"> 
            <a  style="margin-top: 110%;"  href="../login.html">Cerrar sesión</a>
        </div>

    </div>

 <!-- PARTE AZUL DE CONTENIDO -->

    <div class="bottom-section">
        <div>
            <form id="searchForm">
                <input style="background-color: #ffffff; display: inline-block;" type="archivo" id="archivo" name="archivo" placeholder="Buscar" required>
                <button type="button" id="searchButton" style=" display: inline-block; margin-left: -40px; background-image: url('../images/buscar.png'); background-size: cover; background-repeat: no-repeat; border: none; background-color: transparent; border-radius: 0;"> </button>
            </form>
        </div>
        
        <div>
        <div id="resultadosBusqueda">
        <?php
            session_start(); // Iniciar la sesión

            $serverName = "IA-27";
            $connectionInfo = array(
                "Database"=> "NexusEducation",
                "UID"=> "sa",
                "PWD"=> "20SQL22",
                "CharacterSet" => "UTF-8"
            );

            // Establecer la conexión a la base de datos
            $conn = sqlsrv_connect($serverName, $connectionInfo);

            // Verificar la conexión
            if ($conn === false) {
                die("No se pudo establecer la conexión: " . print_r(sqlsrv_errors(), true));
            }

            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
                $sqlGetUserInfo = "SELECT userID FROM [User] WHERE email = ?";
                $paramsGetUserInfo = array($email);
                $stmtGetUserInfo = sqlsrv_query($conn, $sqlGetUserInfo, $paramsGetUserInfo);
                if ($stmtGetUserInfo === false) {
                    die("Error al obtener la información del usuario: " . print_r(sqlsrv_errors(), true));
                } else {
                    $row = sqlsrv_fetch_array($stmtGetUserInfo, SQLSRV_FETCH_ASSOC);
                    $userID = $row['userID'];

                    // Consulta SQL para obtener los archivos subidos por el usuario
                    $sqlGetUserFiles = "SELECT fileType, fileName, filePath, authorizationStatus, fileUploadDate FROM Files WHERE userID = ? ORDER BY fileUploadDate DESC";
                    $paramsGetUserFiles = array($userID);
                    $stmtGetUserFiles = sqlsrv_query($conn, $sqlGetUserFiles, $paramsGetUserFiles);

                    if ($stmtGetUserFiles === false) {
                        die("Error al obtener los archivos del usuario: " . print_r(sqlsrv_errors(), true));
                    } else {
                        // Inicio de la tabla
                        echo "<table class='sin-borde'>";
                        echo "<tbody>";

                        // Iterar sobre los resultados y construir las filas de la tabla
                        while ($row = sqlsrv_fetch_array($stmtGetUserFiles, SQLSRV_FETCH_ASSOC)) {
                            $fileType = $row['fileType'];
                            $fileName = $row['fileName'];
                            $filePath = $row['filePath'];
                            $authorizationStatus = $row['authorizationStatus'];
                            $fileUploadDate = $row['fileUploadDate'];
                            $fileUploadDate = $fileUploadDate->format('Y-m-d H:i:s');

                            // Determinar la imagen del archivo según el tipo de archivo
                            $imageSrc = "";
                            switch ($fileType) {
                                case "PDF":
                                    $imageSrc = "../images/Archivo_Pdf.png";
                                    break;
                                case "DOCX":
                                    $imageSrc = "../images/Archivo_Word.png";
                                    break;
                                case "PPTX":
                                    $imageSrc = "../images/Archivo_Powerpoint.png";
                                    break;
                                case "XLSX":
                                    $imageSrc = "../images/Archivo_Xls.png";
                                    break;
                                // Agregar más casos según los tipos de archivo que tengas
                                default:
                                    $imageSrc = "../images/Archivo_Desconocido.png"; // Si el tipo de archivo es desconocido
                            }

                            // Imprimir la fila de la tabla con el ícono correspondiente y el nombre del archivo
                            echo "<tr>";
                            echo "<td><a href='view-file.html?id=$filePath&nombre=$fileName'>";
                            echo "<img src='$imageSrc' alt='Archivo' class='fotosarchivo'>";
                            echo "</a></td>";
                            echo "<td>$fileName</td>";
                            echo "<td>$authorizationStatus</td>";
                            echo "<td>$fileUploadDate</td>";
                            echo "</tr>";
                        }

                        // Fin de la tabla
                        echo "</tbody>";
                        echo "</table>";
                    }
                }
            }

            // Cerrar la conexión
            sqlsrv_close($conn);
            ?>
        </div>
        </div>
    </div>

    <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>¿Qué deseas hacer?</p>
        <div>
            <button id="deleteButton" class="modal-button">Eliminar</button>
        </div>
        <div>    
            <button id="editButton" class="modal-button">Modificar</button>
        </div>
    </div>
</div>
    <!--COLOR AL DAR CLIC EN ALGUNA DEL DISEÑO 676767  -->


</div>


  <script>
  // Esperar a que el documento esté completamente cargado
  document.addEventListener("DOMContentLoaded", function() {
    // Obtener el formulario y el botón de búsqueda por su ID
    var searchForm = document.getElementById("searchForm");
    var searchButton = document.getElementById("searchButton");
    var archivoInput = document.getElementById("archivo");

    // Agregar un event listener al botón de búsqueda
    searchButton.addEventListener("click", function() {
      buscarArchivos();
    });

    // Agregar un event listener para la tecla Enter en el campo de búsqueda
    archivoInput.addEventListener("keyup", function(event) {
      // Verificar si la tecla presionada es la tecla Enter (código de tecla 13)
      if (event.keyCode === 13) {
        // Prevenir el comportamiento predeterminado del Enter
        event.preventDefault();
        
        // Realizar la búsqueda de archivos
        buscarArchivos();
      }
    });

    // Función para realizar la búsqueda de archivos
    function buscarArchivos() {
      console.log("Buscar archivos...");
      // Obtener el valor del campo de búsqueda
      var searchTerm = archivoInput.value;

      // Crear una nueva instancia de XMLHttpRequest
      var xhr = new XMLHttpRequest();

      // Configurar la solicitud
      xhr.open("GET", "search-my-files.php?term=" + searchTerm, true);

      // Configurar la función de callback para manejar la respuesta del servidor
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          // Limpiar la tabla
          document.getElementById("resultadosBusqueda").innerHTML = "";

          // Agregar los resultados de la búsqueda a la tabla
          document.getElementById("resultadosBusqueda").innerHTML = xhr.responseText;
        }
      };

      // Enviar la solicitud
      xhr.send();
    }

    // Obtener todas las filas de la tabla
    var rows = document.querySelectorAll("table tbody tr");

    // Agregar un event listener para el clic en cada fila
    rows.forEach(function(row) {
      row.addEventListener("click", function() {
        // Restablecer el color de fondo de todas las filas
        rows.forEach(function(row) {
          row.style.backgroundColor = ""; // Restablecer el color de fondo a su valor predeterminado
        });

        // Establecer el color de fondo de la fila seleccionada
        this.style.backgroundColor = "#676767";
      });
    });

    // Agregar un event listener para el doble clic en cada fila
    rows.forEach(function(row) {
      row.addEventListener("dblclick", function() {
        // Mostrar el modal
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
      });
    });

    // Cerrar el modal cuando se hace clic en la "x"
    var closeButton = document.getElementsByClassName("close")[0];
    closeButton.addEventListener("click", function() {
      var modal = document.getElementById("myModal");
      modal.style.display = "none";
    });

    // Cerrar el modal cuando se hace clic fuera de él
    window.onclick = function(event) {
      var modal = document.getElementById("myModal");
      if (event.target == modal) {
        modal.style.display = "none";
      }
    };

    // Manejar el clic en el botón de eliminar
    var deleteButton = document.getElementById("deleteButton");
    deleteButton.addEventListener("click", function() {
      // Aquí puedes agregar la lógica para eliminar el archivo
      alert("Archivo eliminado");
    });

    // Manejar el clic en el botón de modificar
    var editButton = document.getElementById("editButton");
    editButton.addEventListener("click", function() {
      // Aquí puedes agregar la lógica para modificar el archivo
      alert("Archivo modificado");
    });
  });
</script>



</body>
</html>
