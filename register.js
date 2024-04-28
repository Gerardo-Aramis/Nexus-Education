const nombre = document.getElementById("nombre");
const apellidoPaterno = document.getElementById("ApellidoPaterno");
const apellidoMaterno = document.getElementById("ApellidoMaterno");
const correo = document.getElementById("email");
const sexo = document.getElementById("Sexo");
const carrera = document.getElementById("Carrera"); // Corregido el nombre de la variable
const noControl = document.getElementById("NoControl");
const contrasenia = document.getElementById("password");
const contrasenia2 = document.getElementById("repetirPassword");
const terminosYcondiciones = document.getElementById("termsAndConditions");
const form = document.getElementById("form");
const listInputs = document.querySelectorAll(".form-input");

form.addEventListener("submit", (e) => {
    e.preventDefault();
    let condicion = validacionForm();
    if (condicion) {
        enviarFormulario();
    }
});

function validacionForm() {
    let condicion = true;

    // Validar nombre
    if (nombre.value.trim() == "" || !validarCadena(nombre)) {
        alert("Ingrese su nombre");
        condicion = false;
    }

    // Validar apellido paterno
    if (apellidoPaterno.value.trim() == "" || !validarCadena(apellidoPaterno)) {
        alert("Ingrese su Apellido Paterno");
        condicion = false;
    }

    // Validar apellido materno
    if (apellidoMaterno.value.trim() == "" || !validarCadena(apellidoMaterno)) {
        alert("Ingrese su Apellido Materno");
        condicion = false;
    }

    // Validar número de control
    if (noControl.value.trim() == "" || !validarNoControl()) {
        alert("Ingrese su número de control");
        condicion = false;
    }

    // Validar correo
    if (correo.value.trim() == "" || !validarEmail()) {
        alert("Ingrese su correo institucional");
        condicion = false;
    }

    // Validar contraseña
    if (contrasenia.value.trim() == "" || !validarContrasenia(contrasenia)) {
        alert("Ingrese su contraseña");
        condicion = false;
    }

    // Verificar si las contraseñas coinciden
    if (contrasenia2.value != contrasenia.value) {
        alert("Las contraseñas no son iguales");
        condicion = false;
    }

    // Verificar si se aceptaron los términos y condiciones
    if (!terminosYcondiciones.checked) {
        alert("Acepte los términos y condiciones");
        condicion = false;
    }

    return condicion;
}

function enviarFormulario() {
    alert("Enviado")
}

function validarEmail() {
    const validEmail = /^\w+@ittepic.edu.mx+$/;
    return validEmail.test(correo.value);
}

function validarNoControl() {
    const validarNoControl = /^[0-9]{8}$/;
    return validarNoControl.test(noControl.value);
}

function validarCadena(cadena) {
    const validarCadena = /^[A-ZÑa-zñáéíóúÁÉÍÓÚ° ]+$/;
    return validarCadena.test(cadena.value);
}

function validarContrasenia(cadena) {
    const validarPassword = /(?=.*[0-9])(?=.*[\!@#$%^&*()[\]{}\-_+=|:;"'<>,./?])(?=.*[a-z])(?=.*[A-Z]).{8,}/;
    return validarPassword.test(cadena.value);
}
