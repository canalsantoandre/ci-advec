
	function validar(){
		
		if ($("#txtNome").val() == ""){
			alert ('Informe o nome');
			$("#txtNome").focus();
			return false;
		}
		
		if ($("#txtEmail").val() == ""){
			alert ('Informe o email');
			$("#txtEmail").focus();
			return false;
		}
				
				
		$("#frmContato").action="contato-enviar.php";
		
		if (validar()){
			$("#frmContato").submit();		
		}	
	}
	
	
	grecaptcha.ready(function() {
		grecaptcha.execute('6LchCgAVAAAAABm_Jpu0ylIOzsAsOBYM7Tn7FwID', {action: 'homepage'}).then(function(token) {
			document.getElementById("g-token").value = token;
		});
	});
	