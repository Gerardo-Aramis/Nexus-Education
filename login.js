function validateForm() {
    var user = document.getElementById("user").value;
    var password = document.getElementById("password").value;

    // Validación de campos vacíos
    if (user.trim() === "" || password.trim() === "") {
        alert("Por favor, complete todos los campos.");
        return false;
    }

    // Validar el formato del correo electrónico
    if (!validarEmail()) {
        alert("Por favor, ingrese un correo electrónico válido del dominio ittepic.edu.mx.");
        return false;
    }

    // Validar la contraseña
    if (!validarContrasenia(password)) {
        alert("La contraseña debe contener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
        return false;
    }

    return true; // Si todas las validaciones pasan, permite el envío del formulario
}

function validarEmail() {
    var correo = document.getElementById("user");

    // Define la expresión regular
    var validEmail = /^\w+@ittepic.edu.mx+$/;

    // Validamos que sea una cadena válida
    if (validEmail.test(correo.value)) {
        return true;
    } else {
        return false;
    }
}

function validarContrasenia(cadena) {
    var validarPassword = /(?=(.*[0-9]))(?=.*[\!@#$%^&*()\\[\]{}\-_+=|:;"'<>,./?])(?=.*[a-z])(?=(.*[A-Z]))(?=(.*)).{8,}/;
    return validarPassword.test(cadena);
}