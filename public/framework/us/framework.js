$(document).ready(function () {

    // Suprime os popups de alerta nativos do DataTables ("DataTables warning: table id=... - Ajax error")
    if (window.jQuery && $.fn && $.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
    }
    $(document).on('error.dt', function (e, settings, techNote, message) {
        console.warn('DataTables Error (suprimido alerta nativo):', message);
        var tabelaId = (settings && settings.sTableId) ? '#' + settings.sTableId : 'tabela';
        var msgAmigavel = 'Falha ao carregar dados da tabela ' + tabelaId + '. Verifique a conexão ou tente atualizar a página.';

        if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Aviso DataTables (' + tabelaId + ')', msgAmigavel);
        } else if (typeof $.messageAlert === 'function') {
            $.messageAlert({
                tipoMensagem: 2,
                mensagemDestaque: 'Erro na Tabela ' + tabelaId,
                mensagem: msgAmigavel,
                temporizador: 8000
            });
        }
    });

    // Previne o erro do Chrome 130+ ("Blocked aria-hidden on an element because its descendant retained focus")
    $(document).on('show.bs.modal', '.modal', function () {
        $(this).removeAttr('aria-hidden');
    });

    $(document).on('hide.bs.modal', '.modal', function () {
        if (document.activeElement && document.activeElement !== document.body) {
            document.activeElement.blur();
        }
    });

    $(document).on('hidden.bs.modal', '.modal', function () {
        $(this).attr('aria-hidden', 'true');
    });

    if ($.widget != undefined) {
        $.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
            _title: function (title) {
                var $title = this.options.title || '&nbsp;';
                if (("title_html" in this.options) && this.options.title_html == true)
                    title.html($title);
                else title.text($title);
            }
        }));
    }

});

var TipoRetorno = {
    Day: 1,
    Year: 2
}

var FrameworkModal = new function () {

    this.getModalDelete = function (obj) {

        var data = "geral_titulo=" + obj.data('titulo') +
        "&geral_mensagem=" +  obj.data('mensagem') + 
        "&geral_link=" +  obj.data('link');

        $("#divModalDelete").html('');

        $.ajax({
            type: 'GET',
            url: '/geral/getModalDelete/',
            data: data,
            dataType: 'json',
            success: function (rt) {
                if (rt.erro == '0') {
                    $("#divModalDelete").html(rt.modal);
                }
            },
            error: function (rt) {
                $("#divModalDelete").hide();
                $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
            }
        });
    }

    this.getModalConfirm = function (obj) {

        var data = "geral_titulo=" + obj.data('titulo') +
        "&geral_mensagem=" +  obj.data('mensagem') + 
        "&geral_link=" +  obj.data('link');

        $("#divModalDelete").html('');

        $.ajax({
            type: 'GET',
            url: '/geral/getModalConfirm/',
            data: data,
            dataType: 'json',
            success: function (rt) {
                if (rt.erro == '0') {
                    $("#divModalDelete").html(rt.modal);
                }
            },
            error: function (rt) {
                $("#divModalDelete").hide();
                $.messageAlert({ mensagem: rt.mensagem, temporizador: 10000, tipoMensagem: 2 });
            }
        });
    }
}

var FrameworkUi = new function () {

    this.AbrirModalUiDeletar = function (jqueryModal, title, actionOnConfirm, actionOnCancel) {

        jqueryModal.removeClass('hide').dialog({
            resizable: false,
            modal: true,
            title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i> " + title + "</h4></div>",
            title_html: true,
            buttons: [
                {
                    html: "<i class='icon-trash bigger-110'></i>&nbsp; Confirmar",
                    "class": "btn btn-danger btn-xs",
                    click: function () {

                        if (actionOnConfirm != undefined)
                            actionOnConfirm();

                        $(this).dialog("close");
                    }
                }
                ,
                {
                    html: "<i class='icon-remove bigger-110'></i>&nbsp; Cancelar",
                    "class": "btn btn-xs",
                    click: function () {

                        if (actionOnCancel != undefined)
                            actionOnCancel();

                        $(this).dialog("close");
                    }
                }
            ]
        });

    };

    this.AbrirModalUiForm = function (jqueryModal, title, actionOnConfirm, actionOnCancel, labelButtonConfirm, labelButtonCancel) {

        jqueryModal.removeClass('hide').dialog({
            resizable: false,
            modal: true,
            title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i> " + title + "</h4></div>",
            title_html: true,
            buttons: [
                {
                    html: "<i class='icon-save bigger-110'></i>&nbsp; " + labelButtonConfirm,
                    "class": "btn btn-danger btn-xs",
                    click: function () {

                        if (actionOnConfirm != undefined)
                            actionOnConfirm();

                        $(this).dialog("close");
                    }
                }
                ,
                {
                    html: "<i class='icon-remove bigger-110'></i>&nbsp; " + labelButtonCancel,
                    "class": "btn btn-xs",
                    click: function () {

                        if (actionOnCancel != undefined)
                            actionOnCancel();

                        $(this).dialog("close");
                    }
                }
            ]
        });

    };
};

var Framework = new function () {

    this.soNumeros = function (str) {
        return parseInt(str.replace(/[^0-9]/g, ''));
    }

    this.getMoney = function (str) {
        return parseInt(str.replace(/[\D]+/g, ''));
    }

    this.round = function (num, decimals) {
        var t = Math.pow(10, decimals);
        return (Math.round((num * t) + (decimals > 0 ? 1 : 0) * (Math.sign(num) * (10 / Math.pow(100, decimals)))) / t).toFixed(decimals);
    }

    this.formatReal = function (int) {
        var tmp = int + '';
        tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
        if (tmp.length > 6)
            tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
        return tmp;
    }

    this.DateDiff = function (pDateIni, pDateFim, pTipoRetorno) {

        var dataInicio = this.StringToDate(pDateIni + ' 00:00:01');
        var dataTermino = this.StringToDate(pDateFim + ' 23:59:59')

        var dateI = new Date(dataInicio)
        var dateT = new Date(dataTermino)

        var diff = new Date(dateT - dateI);
        // date difference in days
        var days = diff / 1000 / 60 / 60 / 24;
        var years = diff / 1000 / 60 / 60 / 24 / 365;

        var retorno = 0;
        if (pTipoRetorno == TipoRetorno.Day) {
            retorno = days;
        }

        if (isNullOrEmpty(pTipoRetorno)) { pTipoRetorno = TipoRetorno.Day }

        switch (pTipoRetorno) {
            case (TipoRetorno.Day):
                retorno = days;
                break;
            case (TipoRetorno.Year):
                retorno = years;
                break;
            default:
                retorno = days;
        }

        return parseInt(retorno);

    }
    this.StringToDate = function (stringDate) {

        //yyyy-MM-dd HH:mm:ss
        var date = new Date(stringDate.substring(6, 10), parseInt(stringDate.substring(3, 5)) - 1, stringDate.substring(0, 2),
            stringDate.substring(11, 13), stringDate.substring(14, 16), stringDate.substring(17, 19));

        return date;
    }

    this.StringDateToDate = function (stringDate) {

        //yyyy-MM-dd HH:mm:ss
        var date = new Date(stringDate.substring(0, 4), stringDate.substring(5, 7) - 1, stringDate.substring(8, 10),
            stringDate.substring(11, 13), stringDate.substring(14, 16), stringDate.substring(17, 19));

        return date;
    }

    this.AjaxPackJson = function (route, objectData, callbackDone, callbackFail) {
        $.ajax({
            type: "POST",
            url: route,
            processData: true,
            data: objectData,
            preparingMessageHtml: "Aguarde um momento ...",
            success: callbackDone,
            error: callbackFail
        });

        //$.post(route, objectData, callbackDone, callbackFail);
    }

    this.ajaxCallFileDownload = function (methodServer, objectData, callbackDone, callbackFail) {

        var url = methodServer;

        $.fileDownload(url, {
            httpMethod: "POST",
            data: objectData,

            failMessageHtml: "<br><b>Ocorreu um erro ao gerar o relatório, por favor tente novamente em alguns minutos.</b>",
            successCallback: callbackDone,
            failCallback: callbackFail

        });
    }

    this.UnpackJson2 = function (stringJson) {
        var objeto = JSON.parse(decodeURIComponent(stringJson));
        return objeto;
    }

    this.PackJson2 = function (objeto) {
        var stringJson = encodeURIComponent(JSON.stringify(objeto));
        return stringJson;
    }

    this.Guid = function () {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
            s4() + '-' + s4() + s4() + s4();
    }

};

// Regex validate date
var matchdata = new RegExp(/((0[1-9]|[12][0-9]|3[01])\/(0[13578]|1[02])\/[12][0-9]{3})|((0[1-9]|[12][0-9]|30)\/(0[469]|11)\/[12][0-9]{3})|((0[1-9]|1[0-9]|2[0-8])\/02\/[12][0-9]([02468][1235679]|[13579][01345789]))|((0[1-9]|[12][0-9])\/02\/[12][0-9]([02468][048]|[13579][26]))/gi);

// Regex validate hour
var matchhora = new RegExp(/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/gi);

// Regex validate e-mail
var reEmail = new RegExp(/^[a-zA-Z0-9._%+-]+@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}$/gi);

// Regex validate URL
var reUrl = new RegExp(/^(https?):\/\/([a-zA-Z0-9_-]+)(\.[a-zA-Z0-9_-]+)+(\/[a-zA-Z0-9_-]+)*\/?$/gi);

// Regex validate phone
var reFone = new RegExp(/\(\d{2}\) \d{4}-\d{4}/);

function isNullOrEmpty(str) {
    var retorno = false;

    retorno = (str == undefined);
    retorno = retorno || (str == null);
    retorno = retorno || (str == '');
    return retorno;
}

function emptyIfNull(str) {

    if (str == null) {
        str = "";
    }

    return str;
}

function replace(str, f, r) {
    return str.split(f).join(r);
}

function isInt(n) {
    return (n % 1) == 0;
}

function isDate(data) {

    var dataRetorno = (data.match(matchdata));

    return dataRetorno != null;
}

function isEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}

function toIntOrZero(value) {

    if (value == undefined)
        value = 0;

    var nValue = parseInt(value, 10);
    nValue = isNaN(nValue) ? 0 : nValue;

    return nValue;
}

String.prototype.toIntOrZero = function () {
    return toIntOrZero(this);
};

String.prototype.format = function () {
    var formatted = this;
    for (var i = 0; i < arguments.length; i++) {
        var regexp = new RegExp('\\{' + i + '\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[i]);
    }
    return formatted;
};

Array.prototype.sortByProp = function (p) {
    return this.sort(function (a, b) {
        return (a[p] > b[p]) ? 1 : (a[p] < b[p]) ? -1 : 0;
    });
};

Array.prototype.findAll = function (iterator, context) {
    var results = [];
    this.each(function (value, index) {
        if (iterator.call(context, value, index))
            results.push(value);
    });
    return results;
};

if (!Array.prototype.forEach) {
    Array.prototype.forEach = function (fn, scope) {
        for (var i = 0, len = this.length; i < len; ++i) {
            fn.call(scope, this[i], i, this);
        }
    };
}

String.prototype.convertToBoolean = function () {
    return (/^true$/i).test(this);
};

Date.prototype.addDays = function (days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
}
