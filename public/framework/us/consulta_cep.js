jQuery(function ($) {
	$('.cep').mask('00000-000');
});

$(document).ready(function() {
	ConsultaCep.SetListenersConsultaCep();
});


var ConsultaCep = new function (){

	this.limpa_formulário_cep = function () {
        // Limpa valores do formulário de cep.
        $("[id='t_logradouro']").val("");
        $("[id*='t_bairro']").val("");
        $("[id*='t_cidade']").val("");
        $("[id*='t_uf']").val("");   
    }   

    this.SetListenersConsultaCep = function () {

    	$("#t_cep").blur(function () {
    		$(this).val($(this).val().replace(/\D/g, ''));
		/*
		});

    	$("#btnConsultaCEP").bind("click", function (event) {
		*/
	        //$("#Cep").blur(function() {

	        //Nova variável "cep" somente com dígitos.
	        var cep = $("[id*='t_cep']").val().replace(/\D/g, '');

	        //Verifica se campo cep possui valor informado.
	        if (cep != "") {

	            //Expressão regular para validar o CEP.
	            var validacep = /^[0-9]{8}$/;

	            //Valida o formato do CEP.
	            if (validacep.test(cep)) {

	                //Preenche os campos com "..." enquanto consulta webservice.
	                $("[id='t_logradouro']").val("...");
	                $("[id*='t_bairro']").val("...");
	                $("[id*='t_cidade']").val("...");
	                $("[id='t_uf']").val("");;

	                //Consulta o webservice viacep.com.br/
	                $.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

	                	if (!("erro" in dados)) {
	                        //Atualiza os campos com os valores da consulta.
	                        $("[id='t_logradouro']").val(dados.logradouro);
	                        $("[id='t_bairro']").val(dados.bairro);
	                        $("[id='t_cidade']").val(dados.localidade);
	                        $("[id='t_uf']").val(dados.uf);


	                        $.messageAlert({ mensagem: "CEP localizado com sucesso", temporizador: 1000, tipoMensagem: 1, appendMessage: false });
	                    } //end if.
	                    else {
	                        //CEP pesquisado não foi encontrado.
	                        ConsultaCep.limpa_formulário_cep();                        
	                        $.messageAlert({ mensagem: "CEP não encontrado.", temporizador: 5000, tipoMensagem: 3, appendMessage: false });
	                    }
	                });
	            } //end if.
	            else {
	                //cep é inválido.
	                ConsultaCep.limpa_formulário_cep();                
	                $.messageAlert({ mensagem: "Formato de CEP Inválido!", temporizador: 5000, tipoMensagem: 3, appendMessage: false }); //alert("Formato de CEP inválido.");
	            }
	        } //end if.
	        else {
	            //cep sem valor, limpa formulário.
	            //ConsultaCep.limpa_formulário_cep();
	        }
	    });
    }
}