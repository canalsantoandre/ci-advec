$(document).ready(function () {
    Usuario.SetListeners();
});

var Usuario = new function () {
   
    this.SetListeners = function () {
        $("[id='btnReiniciarSenha']").on("click", function (e) {
            e.preventDefault();
            geraModalConfirmaReset($(this));
        });    
        
        $("[id='txtUsuario']").on("blur", function (e) {
            var str = $(this).val().replace(/\s/g, '');
   
            $(this).val(str); 
        });

    }    

    this.SetListenersModal = function () {
        $("[id='btnConfirmaReset']").on("click", function (e) {
            e.preventDefault();
            resetSenha();
        });
    }
    
}

/***********************************************/
/*MODAL*/
/***********************************************/
function geraModalConfirmaReset(obj) {

    var data = "hash_user=" + obj.data('hash-user');
    $("#divModal").html('');

    $.ajax({
        type: 'GET',
        url: obj.data('baseurl') ,
        data: data,
        dataType: 'json',
        success: function (rt) {
            if (rt.erro == '0') {
                $("#divModal").html(rt.modal);
                Usuario.SetListenersModal();
            }
        },
        error: function (rt) {
            $("#divModal").hide();
            $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
        }
    });
}

function resetSenha() {

    $.messageAlert.RemoverAllMessagens();

    $.ajax({
        type: 'POST',
        url: $("#frm_usuario_reset").data('url'),
        data: $("#frm_usuario_reset").serialize(),
        dataType: 'json',
        success: function (rt) {
            if (rt.erro == '0') {
                $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 1 });
            } else {
                $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
            }
        },
        error: function (rt) {
            $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
        }
    });
}

