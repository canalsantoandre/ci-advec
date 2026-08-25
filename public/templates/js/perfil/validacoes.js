$(document).ready(function () {
    PerfilValidacoes.SetListeners();
});

var PerfilValidacoes = new function () {

    this.SetListeners = function () {

        $(document).on("change click", "[id*='chkModuloAcao_']", function (e) {
            e.stopPropagation();
            var targetUrl = $(this).data("url") || $(this).data("baseurl");
            var data = "id_perfil=" + $(this).data("id-perfil") +
                       "&id_modulo=" + $(this).data("id-modulo") +
                       "&modulo_acao=" + encodeURIComponent($(this).data("modulo-acao") || '') +
                       "&status=" + ($(this).is(":checked") ? 1 : 0);
            
            PerfilValidacoes.salvarStatus(targetUrl, data);
        });

        $(document).on("change click", "[id*='chkModulo_']", function (e) {
            e.stopPropagation();
            var targetUrl = $(this).data("url") || $(this).data("baseurl");
            var idModulo  = $(this).data("id-modulo");
            var isChecked = $(this).is(":checked");

            var data = "id_perfil=" + $(this).data("id-perfil") +
                       "&id_modulo=" + idModulo +
                       "&nome_modulo=" + encodeURIComponent($(this).data("nome-modulo") || '') +
                       "&status=" + (isChecked ? 1 : 0);

            PerfilValidacoes.salvarStatus(targetUrl, data);

            if (isChecked) {
                $("#trPerfil" + idModulo).show();
            } else {
                $("#trPerfil" + idModulo).hide();
                $("[id*='chkModuloAcao_" + idModulo + "_']").prop('checked', false);
            }
        });
    };

    this.salvarStatus = function (url, data) {
        if (!url) {
            console.error('URL de atualização não informada.');
            return;
        }

        if (typeof $.messageAlert !== 'undefined' && $.messageAlert.RemoverAllMessagens) {
            $.messageAlert.RemoverAllMessagens();
        }

        $.ajax({
            type: 'GET',
            url: url,
            data: data,
            dataType: 'json',
            success: function (rt) {
                if (rt.erro == '0') {
                    if (typeof USToast !== 'undefined' && USToast.show) {
                        USToast.show('success', 'Permissão Salva', rt.mensagem);
                    } else if (typeof $.messageAlert === 'function') {
                        $.messageAlert({ mensagem: rt.mensagem, temporizador: 5000, tipoMensagem: 1 });
                    }
                } else {
                    if (typeof USToast !== 'undefined' && USToast.show) {
                        USToast.show('error', 'Erro ao Salvar', rt.mensagem);
                    } else if (typeof $.messageAlert === 'function') {
                        $.messageAlert({ mensagem: rt.mensagem, temporizador: 5000, tipoMensagem: 2 });
                    }
                }
            },
            error: function (rt) {
                if (typeof USToast !== 'undefined' && USToast.show) {
                    USToast.show('error', 'Falha na Requisição', 'Erro ao comunicar com o servidor.');
                } else if (typeof $.messageAlert === 'function') {
                    $.messageAlert({ mensagem: 'Erro de comunicação ao salvar permissão.', temporizador: 5000, tipoMensagem: 2 });
                }
            }
        });
    }
};

// Aliases globais para retrocompatibilidade
var Produto = PerfilValidacoes;