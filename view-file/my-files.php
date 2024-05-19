<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Mis Archivos - Nexus Education</title>
  <link rel="stylesheet" href="style-my-files.css">
  <link href="../images/logo.png" rel="shortcut icon">

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

    .sin-borde {
        border-collapse: collapse;
        border: none;
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
            <a href="../buscar/buscar.php">Buscar</a>
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

        // Verificar si se ha iniciado sesión
        session_start();
        
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
                    $sqlGetUserFiles = "SELECT fileType, fileName, filePath, authorizationStatus, fileUploadDate FROM Files WHERE userID = ? AND (authorizationStatus = 'Aceptado' OR 
                    authorizationStatus = 'En Espera' OR authorizationStatus = 'Rechazado') ORDER BY fileUploadDate DESC";
                    $paramsGetUserFiles = array($userID);
                    $stmtGetUserFiles = sqlsrv_query($conn, $sqlGetUserFiles, $paramsGetUserFiles);

                    if ($stmtGetUserFiles === false) {
                        die("Error al obtener los archivos del usuario: " . print_r(sqlsrv_errors(), true));
                    } else {
                        echo "<table id='tabla class='sin-borde'>";
                            echo "<thead>";
                                echo "<tr>";
                                echo "<th>Tipo archivo</th>";
                                echo " <th>Nombre del Archivo</th>";
                                echo " <th>Estatus de Autorización</th>";
                                echo "<th>Fecha de Publicación</th>";
                                echo "</tr>";
                            echo "</thead>";
                        echo "<tbody>";

                        // Iterar sobre los resultados y construir las filas de la tabla
                        while ($row = sqlsrv_fetch_array($stmtGetUserFiles, SQLSRV_FETCH_ASSOC)) {
                            $fileType = $row['fileType'];
                            $fileName = $row['fileName'];
                            $filePath = $row['filePath'];
                            $authorizationStatus = $row['authorizationStatus'];
                            $fileUploadDate = $row['fileUploadDate'];
                            $fileUploadDate = $fileUploadDate->format('Y-m-d H:i:s') . '.' . sprintf('%03d', $fileUploadDate->format('u') / 1000);

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
                                case "XLS":
                                  $imageSrc = "../images/Archivo_Xls.png";
                                    break;
                                case "XLSX":
                                    $imageSrc = "../images/Archivo_Xls.png";
                                    break;
                                // Agregar más casos según los tipos de archivo que tengas
                                default:
                                    $imageSrc = "../images/Archivo_Desconocido.png"; // Si el tipo de archivo es desconocido
                            }

                            // Imprimir la fila de la tabla con el ícono correspondiente y el nombre del archivo
                            echo "<tr data-file-name=\"" . $fileName . "\" data-file-upload-date=\"" . $fileUploadDate . "\">";
                            echo "<td><a href='view-file.html?id=$filePath&nombre=$fileName'>";
                            echo "<img src='$imageSrc' alt='Archivo' class='fotosarchivo'>";
                            echo "</a></td>";
                            echo "<td>$fileName</td>";
                            echo "<td>$authorizationStatus</td>";
                            echo "<td>$fileUploadDate</td>";
                            //COLUMNAS OCULTAS
                            //SUBJETC -> MATERIA
                            //CATEGORY -> CATEGORIA
                            //SEMESTRE
                            //CARRERA
                            //*TRAERME TODO CON SUBCONSULTAS
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

<!--COLOR AL DAR CLIC EN ALGUNA DEL DISEÑO 676767  -->
<div id="modalOpciones" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>¿Qué deseas hacer?</p>
        <div>    
            <button id="modifyButton" class="modal-button">Modificar</button>
        </div>

        <div>
            <button id="deleteButton" class="modal-button">Eliminar</button>
        </div>
        
    </div>
</div>

    <!-- MODAL CONFIRMAR ELIMINACIÓN  -->
    <div id="confirmar-eliminar" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>¿Estás seguro de eliminar la publicación?</p>

        <form id="eliminarForm" action="delete-file.php" method="POST">
            <input type="hidden" name="fileNameDelete" id="fileNameDelete" value="">
           <input type="hidden" name="fileUploadDateDelete" id="fileUploadDateDelete" value="">
            <div>
                <button type="submit" class="modal-button">Sí</button>
            </div>
            <div>
                <button type="button" id="noButton" class="modal-button">No</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CONFIRMAR MODIFICACIÓN  -->
<div id="confirmar-modificar" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>¿Estás seguro de modificar la publicación?</p>
        <form id="modificarForm" action="pre-modify.php" method="POST">
            <input type="hidden" name="fileNameModify" id="fileNameModify" value="">
            <input type="hidden" name="fileUploadDateModify" id="fileUploadDateModify" value="">
            <div>
                <button type="submit" class="modal-button">Sí</button>
            </div>
            <div>
                <button type="button" id="noButton" class="modal-button">No</button>
            </div>
        </form>
    </div>
</div>

</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    //VARIABLES
  var searchButton = document.getElementById("searchButton");
  var archivoInput = document.getElementById("archivo");
  var nombreArchivoSeleccionado;
  var fechaPublicacionSeleccionada;

  // Agregar listeners al cargar la página
  agregarListeners();

  // Definir listeners para los elementos que no cambian
  var primeraModal = document.getElementById("modalOpciones");
  var segundaModal = document.getElementById("confirmar-eliminar");
  var tercerModal = document.getElementById("confirmar-modificar");
  var closeButton1 = primeraModal.querySelector(".close");
  closeButton1.addEventListener("click", function() {
    cerrarModal("modalOpciones");
  });

  //CERRAR ELIMINAR AL DAR CLIC EN LA X
  var closeButton2 = segundaModal.querySelector(".close");
  closeButton2.addEventListener("click", function() {
    cerrarModal("confirmar-eliminar");
  });

  //CERRAR ELIMINAR AL DAR CLIC EN LA X
  var closeButton2 = tercerModal.querySelector(".close");
  closeButton2.addEventListener("click", function() {
    cerrarModal("confirmar-modificar");
  });

  //CAMBIAR DEL MODAL OPCIONES A CONFIRMAR ELIMINAR
  var deleteButton = document.getElementById("deleteButton");
  deleteButton.addEventListener("click", function() {
    cerrarModal("modalOpciones");
    mostrarModal("confirmar-eliminar");
  });
  
  // CAMBIAR DEL MODAL OPCIONES A CONFIRMAR MODIFICAR
var modifyButton = document.getElementById("modifyButton");
modifyButton.addEventListener("click", function() {
  cerrarModal("modalOpciones");
  mostrarModal("confirmar-modificar");
});

  //ACCIONES AL DAR CLIC EN SÍ AL ELIMINAR
  var siButton = document.querySelector("#confirmar-eliminar button:nth-of-type(1)");
  siButton.addEventListener("click", function() {
    cerrarModal("confirmar-eliminar");
  });

  // Obtener referencia al botón "Sí" y agregar un evento de clic
var eliminarButton = document.querySelector("#eliminarForm button[type='submit']");
eliminarButton.addEventListener("click", function(event) {
    // Obtener la fila actual
    var fila = document.querySelector("tr.selected");

    // Obtener los datos de la fila
    var fileName = fila.dataset.fileName;
    var fileUploadDate = fila.dataset.fileUploadDate;

    document.getElementById("fileNameDelete").value = fileName;
    document.getElementById("fileUploadDateDelete").value = fileUploadDate;
    // Enviar el formulario
    document.getElementById("eliminarForm").submit();
});

// Obtener referencia al botón "Sí" y agregar un evento de clic
var modificarButton = document.querySelector("#modificarForm button[type='submit']");
modificarButton.addEventListener("click", function(event) {
    // Evitar que el formulario se envíe automáticamente
    //event.preventDefault();

    // Obtener la fila actual
    var fila = document.querySelector("tr.selected");

    // Obtener los datos de la fila
    var fileName = fila.dataset.fileName;
    var fileUploadDate = fila.dataset.fileUploadDate;

    document.getElementById("fileNameModify").value = fileName;
    document.getElementById("fileUploadDateModify").value = fileUploadDate;

    // Enviar el formulario
    document.getElementById("modificarForm").submit();
});

  //CERRAR MODAL AL DAR CLIC EN NO AL ELIMINAR
  var noButton = document.getElementById("noButton");
  noButton.addEventListener("click", function() {
    cerrarModal("confirmar-eliminar");
  });

  // ACCIONES AL DAR CLIC EN SÍ AL MODIFICAR
var siButton = document.querySelector("#confirmar-modificar button:nth-of-type(1)");
  siButton.addEventListener("click", function() {
    cerrarModal("confirmar-modificar");
  });

// CERRAR MODAL AL DAR CLIC EN NO AL MODIFICAR
var noButton = tercerModal.querySelector("#noButton");
noButton.addEventListener("click", function() {
  cerrarModal("confirmar-modificar");
});

  // Función para agregar listeners a los elementos dinámicos
  function agregarListeners() {
    var rows = document.querySelectorAll("table tbody tr");
    rows.forEach(function(row) {
      row.addEventListener("dblclick", function() {
        mostrarModal("modalOpciones");


      });

      row.addEventListener("click", function() {
        nombreArchivoSeleccionado = row.querySelector('td:nth-child(2)').textContent;
        fechaPublicacionSeleccionada = row.querySelector('td:nth-child(4)').textContent;
        rows.forEach(function(row) {
          row.style.backgroundColor = "";
        });
        
        this.style.backgroundColor = "#676767";

        rows.forEach(function(f) {
                f.classList.remove("selected");
            });
            // Agregar la clase 'selected' a la fila clicada
            this.classList.add("selected");
      });
    });
  }

  // Agregar listener al botón de búsqueda
  searchButton.addEventListener("click", buscarArchivos);

  // Función para buscar archivos
  function buscarArchivos() {
    console.log("Buscar archivos...");
    var searchTerm = archivoInput.value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "search-my-files.php?term=" + searchTerm, true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        document.getElementById("resultadosBusqueda").innerHTML = xhr.responseText;
        agregarListeners(); // Agregar listeners después de cargar los resultados de la búsqueda
      }
    };
    xhr.send();
  }

  // Función para mostrar un modal
  function mostrarModal(idModal) {
    var modal = document.getElementById(idModal);
    modal.style.display = "block";
  }

  // Función para cerrar un modal
  function cerrarModal(idModal) {
    var modal = document.getElementById(idModal);
    modal.style.display = "none";
  }

  // Cerrar el modal cuando se hace clic fuera de él
  window.addEventListener("click", function(event) {
    if (event.target.classList.contains("modal")) {
      cerrarModal("modalOpciones");
      cerrarModal("confirmar-eliminar");
      cerrarModal("confirmar-modificar");
    }
  });

});
</script>

</body>
</html>
