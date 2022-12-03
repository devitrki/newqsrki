/*=========================================================================================
  File Name: apps.js
  Description: All event tabs menu
  initialization and manipulations
  ----------------------------------------------------------------------------------------
  Author: Yudha Permana
==========================================================================================*/

$(document).ready(function () {

    if (global.flagChangePassword == '1') {
        showModal("modalChangePasswordMonthly" + global.dom);
    } else {
        var urlNotif = 'application/notification-system/user';
        $.get(urlNotif, function (res) {
            if (res.status == 'success') {
                if (res.data != null) {
                    $('#notif-title').html(res.data.title);
                    $('#notif-content').html(res.data.content);
                    $("#btnReadNotification").attr("onclick", "notification.read(" + res.data.id + ")");
                    showModal("modalBannerNotification" + global.dom);
                }
            } else {
                console.log(res);
            }
        }, 'json');
    }


    // show time and date
    showTime("#current-time-head");

    // disable enter
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    // set hover hide when clicked
    $('[data-toggle="tooltip"]').tooltip({
        trigger: "hover"
    })

    $('[data-toggle-second="tooltip"]').tooltip({
        trigger: "hover"
    });

    // GET HEIGHT DIV FOR HEIGHT DIV OTHER
    var screen = {
        height: {
            content_main: $('#main-content').outerHeight(),
            navbar: $('.header-navbar').outerHeight(),
            footer: $('.footer').outerHeight(),
        }
    };

    var win_height = $(window).height();
    var content_wrapper_height = win_height - screen.height.navbar - screen.height.footer - 7;

    $('.content-wrapper.tabs').height(content_wrapper_height);
    $('.page_tabs').height(content_wrapper_height);

    global.display.content_wrapper_height = content_wrapper_height;

    global.display.content_tabs_height = global.display.content_wrapper_height - 74;
    $('.fit-content-tabs').height(global.display.content_tabs_height);

    $(window).on('resize', function () {
        screen = {
            height: {
                content_main: $('#main-content').outerHeight(),
                navbar: $('.header-navbar').outerHeight(),
                footer: $('.footer').outerHeight(),
            }
        };
        win_height = $(window).height();
        content_wrapper_height = win_height - screen.height.navbar - screen.height.footer - 7;

        $('.content-wrapper.tabs').height(content_wrapper_height);
        $('.page_tabs').height(content_wrapper_height);
        $(".fit-screen-tabs").height(content_wrapper_height - 32);
        $(".fit-content-tabs").height(content_wrapper_height - 32);

        global.display.content_wrapper_height = content_wrapper_height;

        global.display.content_tabs_height = global.display.content_wrapper_height - 74;
        $('.fit-content-tabs').height(global.display.content_tabs_height);

    });

    /**
     * Global ajax setup to catch status :
     * 401 for login expire
     *
     * Todo: catch 403 and show dialog access forbidden
     */
    $.ajaxSetup({
        global: true,
        type: "POST",
        timeout: (120 * 1000), //Timeout ajax in 30 secs
        headers: {
            'X-CSRF-TOKEN': global.token,
            'time_local': new Date
        },
        statusCode: {
            0: function () {
                loading('stop');
                loadingModal('stop');
            },
            401: function () {
                loading('stop');
                loadingModal('stop');
                messageFailed(global.message.error.failed, global.message.error.abort);
            },
            403: function () {
                loading('stop');
                loadingModal('stop');
                login.show();
            },
            404: function () {
                loading('stop');
                loadingModal('stop');
                messageFailed(global.message.error.failed, "URL " + global.message.error.not_found);
            },
            419: function () {
                loading('stop');
                loadingModal('stop');
                login.show();
            },
            422: function (data) {
                loading('stop');
                loadingModal('stop');
                showErrors($.parseJSON(data.responseText));
            },
            500: function () {
                loading('stop');
                loadingModal('stop');
                messageFailed(global.message.error.failed, global.message.error.internal_server_error);
            },
        }
    });

    // notification
    notification = {
        read: function (id) {
            var urlRead = "application/notification-system/user/read/" + id;
            $.get(urlRead, function (res) {
                location.reload();
            }, 'json');
        }
    };

    // login

    login = {
        show: function () {
            loading("stop");
            $('#lpassword').val("");
            showModal('modalRelogin' + global.dom);
        },
        showChangePassword: function () {
            loading("stop");
            $('#loldpassword').val("");
            $('#lnewpassword').val("");
            $('#lnewpasswordconfirm').val("");
            showModal('modalChangePassword' + global.dom);
        },
        showChangeProfile: function () {
            loading("stop");
            showModal('modalChangeProfile' + global.dom);
        },
        changePassword: function () {
            loadingModal("start", "Change Password");

            var data = {
                old_password: $('#loldpassword').val(),
                new_password: $('#lnewpassword').val(),
                new_password_confirmation: $('#lnewpasswordconfirm').val(),
            }

            $.post('auth/password/change', data, function (res) {
                loadingModal("stop");
                if (res.status == 'success') {
                    hideModal('modalChangePassword' + global.dom);
                    toastr.success(res.message, 'Change Password');
                } else {
                    message.failed(res.message);
                }
            }, 'json');

            return false;
        },
        changePasswordMonthly: function () {
            loadingModal("start", "Change Password Monthly");

            var data = {
                old_password: $('#moldpassword').val(),
                new_password: $('#mnewpassword').val(),
                new_password_confirmation: $('#mnewpasswordconfirm').val(),
            }

            $.post('auth/password/monthly/change', data, function (res) {
                loadingModal("stop");
                if (res.status == 'success') {
                    hideModal("modalChangePasswordMonthly" + global.dom);
                    toastr.success(res.message, 'Change Password Monthly');
                } else {
                    message.failed(res.message);
                }
            }, 'json');

            return false;
        },
        changeProfile: function () {
            loadingModal("start", "Save");

            var data = {
                name: $("#pName").val(),
                phone: $("#pPhone").val(),
            };

            $.post('auth/profile/change', data, function (res) {
                loadingModal("stop");
                if (res.status == 'success') {
                    $("#pfullnamename").html($("#pName").val());
                    hideModal('modalChangeProfile' + global.dom);
                    toastr.success(res.message, 'Change Profile');
                } else {
                    message.failed(res.message);
                }
            }, 'json');

            return false;
        },
        login: function () {
            loadingModal("start", "Relogin");

            var password = $('#lpassword').val();
            if (password == '') {
                loadingModal("stop");

                messageInfo(global.message.validation.password);
                return false;
            }

            var data = {
                email: global.user.email,
                password: password
            }

            $.post('relogin', data, function (res) {

                loadingModal("stop");
                if (res.status != 'success') {
                    $('#lpassword').val("");
                    messageWarning(global.message.validation.credential);
                    return false;
                }

                global.token = res.data.token;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': res.data.token
                    }
                });

                hideModal('modalRelogin' + global.dom);
                toastr.success(global.label.success, 'Relogin');

                // open again menu last
                if (main_menu.myNewTabOpen.length > 0) {
                    var last_menu = main_menu.myNewTabOpen[0];
                    main_menu.openMenuTabs(last_menu.id, last_menu.menu, last_menu.url);
                }

            }, 'json');

            return false;
        }
    }

    // company selected
    company = {
        change: function (user_id, company_id) {
            var data = {
                user_id: user_id,
                company_id: company_id
            };

            $.post('auth/company/change', data, function (x) {
                location.reload();
            }, 'json');
        }
    }


    // languange
    languange = {
        change: function (user_id, languange_id) {
            var data = {
                user_id: user_id,
                languange_id: languange_id
            };

            $.post('auth/languange/change', data, function (x) {
                location.reload();
            }, 'json');
        }
    };

    // event menu

    main_menu = {
        urlCheck: 'auth/check',
        currentTab: 'tab0',
        myNewTabOpen: [],
        tabs: [{
            paneId: 'tab0',
            title: 'Home',
            content: 'home',
            active: true,
            closable: false
        }],
        tabsLiContent: [],
        initMenuTabs: function () {
            main_menu.updateContentLi();
            $('#page_tabs').scrollingTabs({
                tabs: main_menu.tabs, // required,
                propPaneId: 'paneId', // optional - pass in default value for demo purposes
                propTitle: 'title', // optional - pass in default value for demo purposes
                propActive: 'active', // optional - pass in default value for demo purposes
                propContent: 'content', // optional - pass in default value for demo purposes
                cssClassLeftArrow: 'bx bx-chevrons-left',
                cssClassRightArrow: 'bx bx-chevrons-right',
                disableScrollArrowsOnFullyScrolled: true,
                enableSwiping: true,
                tabsLiContent: main_menu.tabsLiContent,
                forceActiveTab: true,
                tabClickHandler: function () { }
            }).on('ready.scrtabs', function () {
                if (main_menu.currentTab != '') {
                    $('a[href$="' + main_menu.currentTab + '"]').trigger("click");
                }
            });
        },
        openMenuTabs: function (id, menu, url) {
            // for if not authentication
            main_menu.myNewTabOpen.push({
                id: id,
                menu: menu,
                url: url
            });

            // clear input
            if ($(".search-input .search-list").hasClass("show")) {
                $('.search-input-close i').trigger("click");
            }
            if ($(".app-content").hasClass("show-overlay")) {
                $(".app-content").removeClass("show-overlay");
            }

            // hide when menu clicked
            var currentBreakpoint = Unison.fetch.now(); // Current Breakpoint

            if (currentBreakpoint.name != 'xl') {
                $.app.menu.hide();
            }

            // check auth
            $.get(main_menu.urlCheck, function (res) {
                if (res.status == 'success') {
                    for (var i = 0; i < main_menu.tabs.length; i++) {
                        if (main_menu.tabs[i].paneId === 'tab' + id) {
                            $('a[href$="tab' + id + '"]').trigger("click");
                            return false;
                        }
                    }
                    var param = false;

                    for (var i = 0; i < url.length; i++) {
                        if (url.charAt(i) == '?') {
                            param = true;
                        }
                    }
                    if (param) {
                        url = url + '&menuid=' + id;
                    } else {
                        url = url + '?menuid=' + id;
                    }
                    newTab = {
                        paneId: 'tab' + id,
                        title: menu,
                        content: url,
                        active: true,
                        closable: true
                    };

                    main_menu.setTabFalse();
                    main_menu.tabs.push(newTab);
                    main_menu.tabsLiContent.push(main_menu.generateLi(newTab));
                    main_menu.currentTab = newTab.paneId;

                    $('#page_tabs').scrollingTabs('refresh');
                }
            }, 'json');
        },
        setTabFalse: function () {
            main_menu.tabs.some(function (tab) {
                if (tab.active) {
                    tab.active = false;
                    return true; // exit loop
                }
            });
        },
        generateLi: function (tab) {
            return '<li role="presentation" class="nav-item"></li>';
        },
        updateContentLi: function () {
            main_menu.tabsLiContent = main_menu.tabs.map(function (tab) {
                return main_menu.generateLi(tab);
            });
        },
        closeTab: function (paneId) {
            for (var i = 0; i < main_menu.tabs.length; i++) {
                if (main_menu.tabs[i].paneId === paneId) {
                    main_menu.tabs.splice(i, 1);
                    main_menu.tabsLiContent.splice(i, 1);
                }
            }
            if (main_menu.tabs.length > 0) {
                if (paneId == main_menu.currentTab) {
                    main_menu.currentTab = main_menu.tabs[main_menu.tabs.length - 1].paneId;
                }
            } else {
                main_menu.currentTab = '';
            }
            $('#page_tabs').scrollingTabs('refresh');
        }
    }

    main_menu.initMenuTabs();
});

// event
$(document).on("click", ".menu-toggle, .modern-nav-toggle", function (e) {
    setTimeout(function () {
        $('#page_tabs').scrollingTabs('refresh');
    }, 10)
});

$("#btnRelogin").on("click", function (e) {
    login.login();
});

$("#btnChangePassword").on("click", function (e) {
    login.changePassword();
});

$("#btnChangePasswordMonthly").on("click", function (e) {
    login.changePasswordMonthly();
});

$("#btnChangeProfile").on("click", function (e) {
    login.changeProfile();
});

$("#btnReloginExit").on("click", function (e) {
    window.location = global.url + '/login';
});

// datatable
$.extend(true, $.fn.dataTable.defaults, {
    responsive: false,
    autoWidth: true,
    scrollX: true,
    scrollCollapse: false,
    language: {
        processing: '<div class="square-loading"><div class="semibold"><span class="bx bx-revision icon-spin text-left"></span>&nbsp;' + global.datatable.loadingRecords + '</div></div>',
        lengthMenu: global.datatable.lengthMenu,
        zeroRecords: global.datatable.zeroRecords,
        emptyTable: "",
        info: global.datatable.info,
        infoEmpty: global.datatable.infoEmpty,
        infoFiltered: global.datatable.infoFiltered,
        infoPostFix: "",
        search: global.datatable.search,
        url: "",
        infoThousands: ",",
        loadingRecords: global.datatable.loadingRecords,
        paginate: {
            sFirst: '<i class="bx bx-chevrons-left"></i>',
            sLast: '<i class="bx bx-chevrons-right"></i>',
            sNext: '<i class="bx bx-chevron-right"></i>',
            sPrevious: '<i class="bx bx-chevron-left"></i>'
        },
        select: {
            rows: "%d " + global.datatable.selected
        }
    },
    dom: 'rt<"row dbottom" <"col-sm-4 d-none d-sm-block"i> <"col-sm-4 col-sm-4 d-none d-sm-block text-center"l> <"col-12 pl-0 col-sm-4 cendiv"p> >',
});
$.fn.dataTable.ext.errMode = 'none';

// event hide alert modal
$(document).on('show.bs.modal', function () {
    hideErrors();
});
