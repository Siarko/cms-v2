let tabs;

const init = function () {
    let b = document.getElementById('blinker');
    let flag = true;
    setInterval(function () {
        if (flag) {
            flag = false;
            b.classList.add('hidden');
        } else {
            flag = true;
            b.classList.remove('hidden');
        }
    }, 700);

    require([
        {
            url: 'views/view',
            onload: function () {
                View.register([
                    'root/users/ViewUserSettings',
                    'root/users/ViewUserList',
                    'root/users/ViewNewUser',

                    'root/pages/ViewPagesList',
                    'root/pages/ViewPageSettings',
                    'root/pages/ViewLocalizationSettings',

                    'root/general/lang/ViewLangList',
                    'root/general/session/ViewSessionSettings',

                    'root/files/ViewFileList'
                ], requireModules());
            }
        }
    ]);
};

const requireModules = function () {
    require([
        'modules/Messages',
        {
            url: 'modules/UrlFiddle',
            onload: function () {
                UrlFiddle.setBasePage('root');
            }
        },
        'modules/ApiQuery',
        'modules/Ui',
        'modules/Users',
        'modules/Pages',
        'modules/Languages',
        'custom/RootController',
        'modules/cssUtil',
        {
            url: 'modules/tabs',
            onload: function () {
                tabs = new TabManager({
                    tabs: 'tab',
                    active: 'tab_users',
                    tabButtons: { //css actions related to buttons activating tabs
                        activeClass: 'active' //no inactive css class = removes active class
                    }
                });
                Messages.appLoaded();
                new jBox('Tooltip', {
                    theme: 'ToolTipRoot',
                    attach: '.niceTooltip',
                    position: {
                        y: 'center',
                        x: 'right',
                    },
                    outside: 'x'
                });
            }
        }
    ]);

};
$(document).ready(init);