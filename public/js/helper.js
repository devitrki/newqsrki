/*=========================================================================================
  File Name: helper.js
  Description: All event in tabs menu content
  ----------------------------------------------------------------------------------------
  Author: Yudha Permana
==========================================================================================*/

// func for loading event
function loading(event, label, ket, identifier) {
    label = typeof label === 'undefined' ? '' : label;
    ket = typeof ket === 'undefined' ? 'loading' : ket;
    identifier = typeof identifier === 'undefined' ? '.tab-content' : identifier;

    if (event != 'start') {
        // stop
        $(identifier).unblock();
    } else {
        // start
        var loading = "";
        if (ket != 'loading') {
            loading = hstring.capitalize(global.label.process) + ' ' + label + '...<br>';
        } else {
            loading = hstring.capitalize(global.label.loading) + ' ...<br>' + label;
        }
        $(identifier).block({
            message: '<div class="semibold"><span class="bx bx-revision icon-spin text-left"></span>&nbsp;' + loading + '</div>',
            overlayCSS: {
                backgroundColor: "#fff",
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: '10px 15px',
                color: '#fff',
                width: 'auto',
                backgroundColor: '#333',
            }
        });
    }
}

function loadingModal(event, label) {
    label = typeof label === 'undefined' ? '' : label;
    if (event != 'start') {
        // stop
        $('.modal-content').unblock();
    } else {
        // start
        $('.modal-content').block({
            message: '<div class="semibold"><span class="bx bx-revision icon-spin text-left"></span>&nbsp;' + global.label.loading + ' ...<br>' + label + '</div>',
            overlayCSS: {
                backgroundColor: "#fff",
                opacity: 0.7,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: '10px 15px',
                color: '#fff',
                width: 'auto',
                backgroundColor: '#333',
            }
        });
    }
}

function showModal(id) {
    $('#' + id).modal('show');
}

function hideModal(id) {
    $('#' + id).modal('hide');
}

function hideShowModal(id_hide, id_show) {
    $('#' + id_hide).on('shown.bs.modal', function () {
        $('#' + id_show).modal('hide');
    });
}

// func show event success
function messageSuccess(title, message) {
    Swal.fire({
        title: title,
        text: message,
        type: "success",
        confirmButtonClass: 'btn btn-secondary',
        allowOutsideClick: false,
        buttonsStyling: false,
        animation: false,
    });
}

// func show event failed
function messageFailed(title, message) {
    Swal.fire({
        title: title,
        text: message,
        type: "error",
        confirmButtonClass: 'btn btn-secondary',
        animation: false,
        allowOutsideClick: false,
        buttonsStyling: false,
    });
}

// func for info
function messageInfo(message) {
    Swal.fire({
        title: "Info!",
        text: message,
        type: "info",
        confirmButtonClass: 'btn btn-secondary',
        buttonsStyling: false,
    });
}

// func for warning
function messageWarning(message) {
    Swal.fire({
        title: "Warning!",
        text: message,
        type: "warning",
        confirmButtonClass: 'btn btn-primary',
        buttonsStyling: false,
    });
}

// func for show error from laravel
function showErrors(errors) {
    $('.errors').removeClass("d-none");
    var lError = "<ul class='mb-0'>";
    $.each(errors, function (key, value) {
        if ($.isPlainObject(value)) {
            $.each(value, function (key, value) {
                lError += '<li>' + value + '</li>';
            });
        }
    });
    lError += '</ul>';
    $('.errors').append(lError);
    $('.modal-body').scrollTop(0);
}

function showError(error) {
    $('.errors').removeClass("d-none");
    var lError = "<ul class='mb-0'>";
    lError += '<li>' + error + '</li>';
    lError += '</ul>';
    $('.errors').append(lError);
    $('.modal-body').scrollTop(0);
}

function showErrors2(id, errors) {
    $('#errors' + id).removeClass("d-none");
    var lError = "<ul class='mb-0'>";
    $.each(errors, function (key, value) {
        if ($.isPlainObject(value)) {
            $.each(value, function (key, value) {
                lError += '<li>' + value + '</li>';
            });
        }
    });
    lError += '</ul>';
    $('#errors' + id).append(lError);
    $('.modal-body').scrollTop(0);
}

function hideErrors() {
    $('.errors').html("");
    $('.errors').addClass("d-none");
}

function hideErrors2(id) {
    $('#' + id).html("");
    $('#' + id).addClass("d-none");
}

// datatable

var message = {
    success: function (message) {
        Swal.fire({
            title: global.label.success,
            text: message,
            type: "success",
            confirmButtonClass: 'btn btn-secondary',
            allowOutsideClick: false,
            buttonsStyling: false,
            animation: false,
        });
    },
    info: function (message) {
        Swal.fire({
            title: global.message.info,
            text: message,
            type: "info",
            confirmButtonClass: 'btn btn-secondary',
            buttonsStyling: false,
        });
    },
    warning: function (message) {
        Swal.fire({
            title: global.message.warning,
            text: message,
            type: "warning",
            confirmButtonClass: 'btn btn-secondary',
            buttonsStyling: false,
        });
    },
    failed: function (message) {
        Swal.fire({
            title: global.message.error.failed,
            text: message,
            type: "error",
            confirmButtonClass: 'btn btn-secondary',
            animation: false,
            allowOutsideClick: false,
            buttonsStyling: false,
        });
    },
    confirm: function (title, text, fcall) {
        Swal.fire({
            title: title,
            text: text,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            confirmButtonClass: 'btn btn-secondary',
            cancelButtonClass: 'btn btn-danger ml-1',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                var callback = fcall + "()";
                eval(callback);
            }
        })
    }
}

var hdatatable = {
    reload: function (id) {
        $("#" + id).DataTable().ajax.reload();
    },
    select: {
        data: function (id) {
            var data = $("#" + id).DataTable().rows({ selected: true }).data();
            return data[0];
        },
        count: function (id) {
            return $("#" + id).DataTable().rows({ selected: true }).count();
        },
        empty: function (id) {
            var count = $("#" + id).DataTable().rows({ selected: true }).count();
            if (count <= 0) {
                return true;
            } else {
                return false;
            }
        }
    }
}

var hstring = {
    capitalize: function (s) {
        if (typeof s !== 'string') return ''
        return s.charAt(0).toUpperCase() + s.slice(1)
    }
}

var dropdown = {
    hide: function (id) {
        $("body").click();
    }
}

/**
 * Show current date and time
 * @param {string} id : id of html element that the output will be displayed
 * @returns {Boolean}
 */
function showTime(id) {
    var date = new Date;
    var year = date.getFullYear();
    var month = date.getMonth();
    var months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    var d = date.getDate();
    var day = date.getDay();
    var days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

    var h = date.getHours();
    if (h < 10) {
        h = "0" + h;
    }

    var m = date.getMinutes();
    if (m < 10) {
        m = "0" + m;
    }

    var s = date.getSeconds();
    if (s < 10) {
        s = "0" + s;
    }
    //result = '' + days[day] + ' ' + months[month] + ' ' + d + ' ' + year + ' ' + h + ':' + m + ':' + s;
    var result = '' + d + ' ' + months[month] + ' ' + year + ' ' + h + ':' + m + ':' + s;
    $(id).text(result);
    setTimeout('showTime("' + id + '");', '1000');
    return true;
}
