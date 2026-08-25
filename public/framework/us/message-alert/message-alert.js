/*
* message-alert
* Copyright 2015 - Elpidio N Lima Junior (UnicSoftware)
*
*/

/*
:::   COMO USAR   :::
 
$.messageAlert({
    callback: function () { },          //<-- function:  Padrão function vazia.      Você pode passar um callback, caso deseje fazer algo sincrono depois da mensagem.
    objRetornoAjax: null,               //<-- object(DTORetornoAjax): Padrão null.   Quando objeto enviado, sobreescreve a mensagem, mensagem destaque e tipo mensagem.
    mensagem: "Mensagem de testes!!",   //<-- String:    Padrão vazio.               Mensagem principal que irá ser exibida.
    mensagemDestaque: "Alerta",         //<-- String:    Padrão vazio.               Mensagem curta, em destaque, que irá aparecer no inicio da mensagem princial.
    tipoMensagem: 4,                    //<-- Int32      Padrão 4 (info).            Tipo de mensagem que será exibida (1 - success | 2 - error | 3 - warning | 4 - info)    
    isPermiteExcluirMesangem: true,     //<-- Boolean:   Padrão true.                true para permitir o botão "exclur" mensagem. false não exibe o botão
    temporizador: 0,                    //<-- Int32:     Padrão 0.                   Tempo que pode ser programado para a mensage desaparecer. 0 é sem tempo.
    qtdLimiteMensagens: 4,              //<-- Int32:     Padrão 4.                   Limite total de mensagens que será exibida na tela. Assume o ultimo valor passado como o default para o escopo.
    objetoMensagem: "divMensagemGlobal",//<-- String:    Padrão divMensagemGlobal    Nome do div onde será mostrado a mensagem
});
   
   

//Esse metodo pode ser chamado caso deseje apagar todas as mensagens
$.messageAlert.RemoverAllMessagens()



//Forma mais simples de utilização:

//Sem mensagem
$.messageAlert();

//Mensagem Simples informativa
$.messageAlert({ mensagem: "Faça o uso correto do sistema." });

//Mensagem Simples de erro de sistema
$.messageAlert({ mensagem: "Erro de null reference", tipoMensagem: 2 });

//Mensagem Simples de erro de negocio, com mensagem destaque
$.messageAlert({ mensagemDestaque: "Preste atenção!", mensagem: "Não é possivel somar um numero com uma letra", tipoMensagem: 3 });


$.messageAlert({ mensagem: "Faça o uso correto do sistema.", qtdLimiteMensagens: 8 });

*/


var limiteMensagens;

(function ($) {


    $.messageAlert = function (options) {

        //1 - success
        //2 - error
        //3 - warning
        //4 - info

        var defaults = {
            objRetornoAjax: null, // Objeto "DTORetornoAjax", caso ele venha preenchido, tem prioridade sobre os campos "Mensagem", "Mensagem Destaque", "Tipo Mensagem"
            mensagem: "",
            mensagemDestaque: "",
            tipoMensagem: 4,
            isPermiteExcluirMesangem: true,
            appendMessage: true, //A mensagem será apendada ou substituida?
            appendAfterOrBefore: 'after', //No caso da mensagem ser apendada, em baixo ou em cima?
            temporizador: 5000,
            qtdLimiteMensagens: 4,
            objetoMensagem: "divMensagemGlobal",
            callback: function () {
            }
        };

        if ((!isNullOrEmpty(options.objRetornoAjax) && options.objRetornoAjax.TipoMensagem != 0 && !isNullOrEmpty(options.objRetornoAjax.TextoMensagem))
            ||
            !isNullOrEmpty(options.mensagem) && !isNullOrEmpty(options.tipoMensagem)
        ) {
            var opts = $.extend(defaults, options);

            limiteMensagens = opts.qtdLimiteMensagens;

            if (opts.objRetornoAjax != null) {
                opts.tipoMensagem = opts.objRetornoAjax.TipoMensagem == null ? "" : opts.objRetornoAjax.TipoMensagem;
                opts.mensagem = opts.objRetornoAjax.TextoMensagem == null ? "" : opts.objRetornoAjax.TextoMensagem;
                opts.mensagemDestaque = opts.objRetornoAjax.TextoMensagemDestaque == null ? "" : opts.objRetornoAjax.TextoMensagemDestaque;
            }



            if (typeof USToast !== 'undefined' && USToast.show) {
                USToast.show(opts.tipoMensagem, opts.mensagemDestaque, opts.mensagem);
            } else {
                exibirMessageAlert(opts);
            }


            if (!isNullOrEmpty(opts.callback)) {
                opts.callback();
            }

            setListenersMesagemAlert();
        }

    };

    $.messageAlert.RemoverAllMessagens = function () {

        var tempoIncremento = 300;
        var tempoAtual = 0;

        $(".message-alert-button-delete").each(function (index, value) {

            tempoAtual += tempoIncremento;

            var idmessage = $(this).attr("idmessage");

            setTimeout(function () {
                $("#" + idmessage).fadeOut(700);
                $("#" + idmessage).remove();
            }, tempoAtual);
        });
    };

})(jQuery);


function setListenersMesagemAlert() {

    $(".message-alert-button-delete").off();
    $(".message-alert-button-delete").on('click', function () {

        var idmessage = $(this).attr("idmessage");

        $("div[id*='" + idmessage + "']").fadeOut(200);

        setTimeout(function () {
            $("div[id*='" + idmessage + "']").remove();
        }, 300);
    });

}

function excluirExcessoMensagens() {

    var selectorMensagemIndividual = $(".message-alert-unic");

    var qtdExcluir = selectorMensagemIndividual.length - limiteMensagens;

    if (qtdExcluir <= 0) {
        return;
    }



    //$(selectorMensagemIndividual.get().reverse()).each(function (index, value) {
    selectorMensagemIndividual.each(function (index, value) {

        if (index > limiteMensagens - 1) {

            $("#" + value.id).remove();
        }

    });

}

function exibirMessageAlert(opts) {

    var classeTipoMensagem;
    switch (opts.tipoMensagem) {
        case 1:
            classeTipoMensagem = "success";
            break;
        case 2:
            classeTipoMensagem = "danger";
            break;
        case 3:
            classeTipoMensagem = "warning";
            break;
        case 4:
            classeTipoMensagem = "info";
            break;
        default:
            classeTipoMensagem = "info";
            break;
    }

    var idMessage = "message-alert-" + new Date().getTime();

    var
        retorno = '<div id="' + idMessage + '" class="message-alert-unic" style="display: none; ">';
    retorno += '    <div class="message-alert message-alert-' + classeTipoMensagem + '">';
    retorno += '        <div class="message-alert-icon-' + classeTipoMensagem + '">';
    retorno += '            &nbsp;';
    retorno += '        </div>';
    retorno += '        <div class="message-alert-separation">';

    if (opts.isPermiteExcluirMesangem) {
        retorno += '            <div idMessage="' + idMessage + '" class="message-alert-button-delete"> &nbsp; </div>';
    } else {
        retorno += '            <div idMessage="' + idMessage + '" class="message-alert-button-delete" style="display:none;"> &nbsp; </div>';
    }

    retorno += '            <div class="message-alert-content">';

    if (!isNullOrEmpty(opts.mensagemDestaque)) {
        retorno += '                <strong>' + opts.mensagemDestaque + '</strong> &nbsp;';
    }

    retorno += opts.mensagem;
    retorno += '            </div>';
    retorno += '            <div style="clear: both;"></div>';
    retorno += '        </div>';
    retorno += '    </div>';
    retorno += '</div>';

    var isExisteMensagemAberta = replace(replace($("div[id*='" + opts.objetoMensagem + "']").html(), " ", ""), "\n", "") == "";

    if (opts.appendMessage) {
        //Caso seja para apendar a mensagem, entra aqui

        if (opts.appendAfterOrBefore == 'before') {
            //Caso seja before, adiciona sempre as mensagem uma em cima da outra

            if (isExisteMensagemAberta) {
                //A primeira mensagem sempre aparece fixa embaixo
                $("div[id*='" + opts.objetoMensagem + "']").append(retorno);
            } else {
                //As outras mensagems sempre aparecem em cima das outras mensagens
                var idUltimaMsg = $("div[id*='" + opts.objetoMensagem + "']").attr("id");

                $("#" + idUltimaMsg).before(retorno);
            }
        } else if (opts.appendAfterOrBefore == 'after') {
            //Apenda as mensagems sempre uma embaixo da outra
            $("div[id*='" + opts.objetoMensagem + "']").append(retorno);
        }


    } else {
        //No caso de substituir a mensagem, sobreescreve a div
        $("div[id*='" + opts.objetoMensagem + "']").html(retorno);
    }

    excluirExcessoMensagens();

    //Faz efeito apenas com a ultima mensagem incluida
    $("#" + idMessage).fadeIn(1000);

    //Quando entra uma mensagem, todas são fechadas e abertas novamente
    //$("#divMensagemSalvarPagamento").append(retorno).hide().fadeIn(1000);

    $("#" + idMessage).fadeIn(1000);

    if (opts.temporizador > 0) {
        $("#" + idMessage).delay(opts.temporizador);
        $("#" + idMessage).fadeOut(opts.temporizador, "linear");
    }

}


