/*jQuery(function ($) {
    $('.date').mask('00/00/0000');
    $('.cep').mask('00000-000');
    $('.cpf').mask('000.000.000-00');
    $('.celular').mask('(00) 0 0000-0000');
});*/

$(document).ready(function () {
    Convidado.SetListeners();
});

var Convidado = new function () {
   
    this.SetListeners = function () {
        
        $(".semespaco").on("blur", function (e) {
            var str = $(this).val().replace(/\s/g, '');
   
            $(this).val(str); 
        });

    }    
    
}
