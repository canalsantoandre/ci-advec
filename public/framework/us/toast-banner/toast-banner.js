/* ====================================================
   GLOBAL TOAST & FLASH BANNER ENGINE
   Framework US — UnicSoftware
   ==================================================== */

var USToast = (function ($) {
    'use strict';

    var timer = null;

    var colorMap = {
        'success': { border: '#198754', text: '#198754', icon: 'bi-check-circle-fill', bg: 'linear-gradient(135deg,#059669,#047857)', cls: 'alert-success', label: 'Sucesso!' },
        'error': { border: '#dc3545', text: '#dc3545', icon: 'bi-exclamation-triangle-fill', bg: 'linear-gradient(135deg,#dc2626,#b91c1c)', cls: 'alert-danger', label: 'Erro!' },
        'warning': { border: '#ffc107', text: '#d39e00', icon: 'bi-exclamation-circle-fill', bg: 'linear-gradient(135deg,#d97706,#b45309)', cls: 'alert-warning', label: 'Atenção!' },
        'info': { border: '#0dcaf0', text: '#0dcaf0', icon: 'bi-info-circle-fill', bg: 'linear-gradient(135deg,#2563eb,#1d4ed8)', cls: 'alert-info', label: 'Informação!' }
    };

    function escHtml(str) {
        return $('<div>').text(str || '').html();
    }

    function ensureContainers() {
        // Ensure Toast container in body
        if ($('#usToastContainer').length === 0) {
            var toastHtml =
                '<div id="usToastContainer" class="us-toast-container" style="display:none;">' +
                '<div id="usToastCard" class="us-toast-card shadow-lg">' +
                '<div id="usToastHeader" class="us-toast-header">' +
                '<span><i id="usToastIcon" class="bi me-2"></i><span id="usToastTitle">MENSAGEM</span></span>' +
                '<button type="button" class="us-toast-close" onclick="USToast.hide()">&times;</button>' +
                '</div>' +
                '<div id="usToastBody" class="us-toast-body"></div>' +
                '</div>' +
                '</div>';
            $('body').append(toastHtml);
        }

        // Ensure dynamic flash banner container at top of main or container
        if ($('#dynamicFlashBannerContainer').length === 0) {
            var $target = $('#main').length ? $('#main') : $('.pagetitle').parent();
            if ($target.length) {
                var $title = $target.find('.pagetitle');
                if ($title.length) {
                    $('<div id="dynamicFlashBannerContainer" class="us-dynamic-flash-container"></div>').insertAfter($title);
                } else {
                    $target.prepend('<div id="dynamicFlashBannerContainer" class="us-dynamic-flash-container"></div>');
                }
            }
        }
    }

    function showToast(type, title, message) {
        ensureContainers();
        var key = (type || 'info').toString().toLowerCase();
        if (key === '1') key = 'success';
        if (key === '2') key = 'error';
        if (key === '3') key = 'warning';
        if (key === '4') key = 'info';

        var cfg = colorMap[key] || colorMap['info'];
        var displayTitle = title || cfg.label;

        $('#usToastCard').css('border-left', '5px solid ' + cfg.border);
        $('#usToastHeader').css('color', cfg.text);
        $('#usToastIcon').attr('class', 'bi ' + cfg.icon + ' me-2');
        $('#usToastTitle').text(displayTitle);
        $('#usToastBody').html(message || '');

        $('#usToastContainer').stop(true, true).fadeIn(300);
        clearTimeout(timer);
        timer = setTimeout(hideToast, 5000);

        showFlashBanner(key, displayTitle, message);
    }

    function hideToast() {
        $('#usToastContainer').fadeOut(300);
    }

    function showFlashBanner(type, title, message) {
        ensureContainers();
        var key = (type || 'info').toString().toLowerCase();
        if (key === '1') key = 'success';
        if (key === '2') key = 'error';
        if (key === '3') key = 'warning';
        if (key === '4') key = 'info';

        var cfg = colorMap[key] || colorMap['info'];
        var displayTitle = title || cfg.label;

        var html = '<div class="alert ' + cfg.cls + ' alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 text-white" style="background:' + cfg.bg + ';" role="alert">' +
            '<div class="d-flex align-items-center">' +
            '<i class="bi ' + cfg.icon + ' fs-3 me-3"></i>' +
            '<div>' +
            '<strong class="d-block fs-6 mb-1">' + escHtml(displayTitle) + '</strong>' +
            '<span>' + escHtml(message) + '</span>' +
            '</div>' +
            '</div>' +
            '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';

        $('#dynamicFlashBannerContainer').html(html);
        //window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    $(document).ready(function () {
        ensureContainers();
    });

    return {
        show: showToast,
        showToast: showToast,
        showFlashBanner: showFlashBanner,
        hide: hideToast
    };
})(jQuery);

// Global alias mappings for transparent project-wide usage
window.usShowToast = function (type, title, message) { USToast.show(type, title, message); };
window.usShowFlashBanner = function (type, title, message) { USToast.showFlashBanner(type, title, message); };
window.peShowToast = window.usShowToast;
window.peShowFlashBanner = window.usShowFlashBanner;
