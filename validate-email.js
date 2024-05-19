const email = document.getElementById('email');

function sendOTP() {

	if(!validarEmail()){
		return;
	}
	
	const otpverify = document.getElementsByClassName('otpverify')[0];

	let otp_val = Math.floor(Math.random() * 10000);

	let emailbody = `<h2>Su código de verificación</h2>${otp_val}`;
	Email.send({
    SecureToken : "b7dd04be-c3a0-46eb-b2bc-cee6813bc20e",
    To : email.value,
    From : "guadalupebe16@gmail.com",
    Subject : "Verifique su identidad",
    Body : emailbody,
}).then(

	message => {
		if (message === "OK") {
			alert("El código de verificación ha sido enviado, revise su correo " + email.value);

			otpverify.style.display = "flex";
			const otp_inp = document.getElementById('otp_inp');
			const otp_btn = document.getElementById('otp-btn');

			otp_btn.addEventListener('click', () => {
				if (otp_inp.value == otp_val) {
					alert("Identidad Verificada");
					window.location.href = "password-recovery.html?email=",email;
				}
				else {
					alert("Código no válido.");
				}
			})
		}
	}
);
}

function validarEmail(){
                
	// Define la expresión regular
	var validEmail =  /^\w+@ittepic.edu.mx+$/;

	// Validamos que sea una cadena válida
	if( validEmail.test(email.value) ){
		return true;
	}else{
		alert('Ingrese un correo con el dominio @ittepic.edu.mx')
		return false;
	}
} 
