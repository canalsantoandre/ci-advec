$(document).ready(function () {
    ConvidadoLinkFoto.SetListeners();
});

var ConvidadoLinkFoto = new function () {

    this.SetListeners = function () {

        this.validaTelaLink = function () {
            var validacao = "";
          
            if ($("#txtConvidadoItemLinkFoto").val() == "") {
                validacao += (validacao != "" ? "," : "") + "Link da foto";
            }
            if ($("#txtConvidadoItemLinkFotoDescricao").val() == "") {
                validacao += (validacao != "" ? "," : "") + "Descrição";
            }
            if (validacao != "") {
                $.messageAlert({ mensagem: "Verifique as informações antes de salvar - (" + validacao + ")", temporizador: 10000, tipoMensagem: 3 });
            }
            return (validacao == "");
        }

        $("[id='btnIncluirLinkFoto']").on("click", function (e) {
            e.preventDefault();
            if (ConvidadoLinkFoto.validaTelaLink()) {
                ConvidadoLinkFoto.salvarLink();
            }

        });

        $("[id='btnApagarLinkFoto']").on("click", function (e) {
            e.preventDefault();
            ConvidadoLinkFoto.excluirLink($(this).data("url"));
        });

        $("[id='btnPreApagarLinkFoto']").on("click", function (e) {
            e.preventDefault();
            $("#mensagem").html($(this).data("mensagem"));
            $("#btnApagarLinkFoto").data("url", $(this).data("url"));
        });

    };

   
    this.salvarLink = function () {
        

        var fd = new FormData();
       
        fd.append('id_convidado', $("#hidIdConvidado").val());
        fd.append('descricao_link_foto', $("#txtConvidadoItemLinkFotoDescricao").val());
        fd.append('link_foto', $("#txtConvidadoItemLinkFoto").val());
        
        $.messageAlert.RemoverAllMessagens();
        $.ajax({
            type: 'POST',
            url: $("#hidUrlInserirLinkFoto").val(),
            data: fd,
            contentType: false,
            processData: false,
            success: function (ret) {

                rt = JSON.parse(ret)
                if (rt.erro == '0') {
                    $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 1 });
                    $("#divLinkFotos").html(rt.objeto_retorno);
                    ConvidadoLinkFoto.SetListeners();
                } else {
                    $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
                }
            },
            error: function (rt) {
                $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
            }
        });
    }
    this.excluirLink = function (url) {

        $.messageAlert.RemoverAllMessagens();
        $.ajax({
            type: 'GET',
            url: url,
            data: null,
            dataType: 'json',
            success: function (rt) {

                if (rt.erro == '0') {
                    $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 1 });
                    $("#divLinkFotos").html(rt.objeto_retorno);
                    ConvidadoLinkFoto.SetListeners();
                } else {
                    $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
                }
            },
            error: function (rt) {
                $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
            }
        });
    }
}
