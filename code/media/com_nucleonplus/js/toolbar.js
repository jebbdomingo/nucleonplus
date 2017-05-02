var domIsReady = (function(domIsReady) {
    var isBrowserIeOrNot = function() {
        return (!document.attachEvent || typeof document.attachEvent === "undefined" ? 'not-ie' : 'ie');
    }

    domIsReady = function(callback) {
        if(callback && typeof callback === 'function'){
            if(isBrowserIeOrNot() !== 'ie') {
                document.addEventListener("DOMContentLoaded", function() {
                    return callback();
                });
            } else {
                document.attachEvent("onreadystatechange", function() {
                    if(document.readyState === "complete") {
                        return callback();
                    }
                });
            }
        } else {
            console.error('The callback is not a function!');
        }
    }

    return domIsReady;
})(domIsReady || {});

(function(document, window, domIsReady, undefined) {
    domIsReady(function() {
        var koowaEditBar = document.createElement('div');
        var dashboardBtn = '';
        var newBtn       = '';
        var editBtn      = '';
        var authBtn      = '';

        // Add ID to HTML element
        document.documentElement.id = 'k-edit-bar-html-element';

        if (Nucleonplus.ToolBar.isAuthentic())
        {
            dashboardBtn = '' +
                '<a id="k-edit-bar__link--dashboard" href="' + Nucleonplus.ToolBar.url.dashboard + '">' +
                '<svg id="k-edit-bar__link--dashboard__svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="28" height="28" viewBox="0 0 28 28"><path d="M4 20.5v3q0 0.203-0.148 0.352t-0.352 0.148h-3q-0.203 0-0.352-0.148t-0.148-0.352v-3q0-0.203 0.148-0.352t0.352-0.148h3q0.203 0 0.352 0.148t0.148 0.352zM4 14.5v3q0 0.203-0.148 0.352t-0.352 0.148h-3q-0.203 0-0.352-0.148t-0.148-0.352v-3q0-0.203 0.148-0.352t0.352-0.148h3q0.203 0 0.352 0.148t0.148 0.352zM4 8.5v3q0 0.203-0.148 0.352t-0.352 0.148h-3q-0.203 0-0.352-0.148t-0.148-0.352v-3q0-0.203 0.148-0.352t0.352-0.148h3q0.203 0 0.352 0.148t0.148 0.352zM28 20.5v3q0 0.203-0.148 0.352t-0.352 0.148h-21q-0.203 0-0.352-0.148t-0.148-0.352v-3q0-0.203 0.148-0.352t0.352-0.148h21q0.203 0 0.352 0.148t0.148 0.352zM4 2.5v3q0 0.203-0.148 0.352t-0.352 0.148h-3q-0.203 0-0.352-0.148t-0.148-0.352v-3q0-0.203 0.148-0.352t0.352-0.148h3q0.203 0 0.352 0.148t0.148 0.352zM28 14.5v3q0 0.203-0.148 0.352t-0.352 0.148h-21q-0.203 0-0.352-0.148t-0.148-0.352v-3q0-0.203 0.148-0.352t0.352-0.148h21q0.203 0 0.352 0.148t0.148 0.352zM28 8.5v3q0 0.203-0.148 0.352t-0.352 0.148h-21q-0.203 0-0.352-0.148t-0.148-0.352v-3q0-0.203 0.148-0.352t0.352-0.148h21q0.203 0 0.352 0.148t0.148 0.352zM28 2.5v3q0 0.203-0.148 0.352t-0.352 0.148h-21q-0.203 0-0.352-0.148t-0.148-0.352v-3q0-0.203 0.148-0.352t0.352-0.148h21q0.203 0 0.352 0.148t0.148 0.352z"></path></svg>' +
                '<span id="k-edit-bar__link--dashboard__text">Dashboard</span>' +
                '</a>';
        }

        if (Nucleonplus.ToolBar.isAuthentic())
        {
            authBtn = '' +
                '<a id="k-edit-bar__link--logout" href="' + Nucleonplus.ToolBar.url.logout + '">' +
                '<svg id="k-edit-bar__link--logout__svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="28" viewBox="0 0 26 28"><path d="M10 22.5q0 0.063 0.016 0.313t0.008 0.414-0.047 0.367-0.156 0.305-0.32 0.102h-5q-1.859 0-3.18-1.32t-1.32-3.18v-11q0-1.859 1.32-3.18t3.18-1.32h5q0.203 0 0.352 0.148t0.148 0.352q0 0.063 0.016 0.313t0.008 0.414-0.047 0.367-0.156 0.305-0.32 0.102h-5q-1.031 0-1.766 0.734t-0.734 1.766v11q0 1.031 0.734 1.766t1.766 0.734h4.875t0.18 0.016 0.18 0.047 0.125 0.086 0.109 0.141 0.031 0.211zM24.5 14q0 0.406-0.297 0.703l-8.5 8.5q-0.297 0.297-0.703 0.297t-0.703-0.297-0.297-0.703v-4.5h-7q-0.406 0-0.703-0.297t-0.297-0.703v-6q0-0.406 0.297-0.703t0.703-0.297h7v-4.5q0-0.406 0.297-0.703t0.703-0.297 0.703 0.297l8.5 8.5q0.297 0.297 0.297 0.703z"></path></svg>' +
                '<span id="k-edit-bar__link--logout__text">Logout</span>' +
                '</a>';
        }
        else
        {
            authBtn = '' +
                '<a id="k-edit-bar__link--logout" href="' + Nucleonplus.ToolBar.url.login + '">' +
                '<svg id="k-edit-bar__link--logout__svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="28" viewBox="0 0 26 28"><path d="M10 22.5q0 0.063 0.016 0.313t0.008 0.414-0.047 0.367-0.156 0.305-0.32 0.102h-5q-1.859 0-3.18-1.32t-1.32-3.18v-11q0-1.859 1.32-3.18t3.18-1.32h5q0.203 0 0.352 0.148t0.148 0.352q0 0.063 0.016 0.313t0.008 0.414-0.047 0.367-0.156 0.305-0.32 0.102h-5q-1.031 0-1.766 0.734t-0.734 1.766v11q0 1.031 0.734 1.766t1.766 0.734h4.875t0.18 0.016 0.18 0.047 0.125 0.086 0.109 0.141 0.031 0.211zM24.5 14q0 0.406-0.297 0.703l-8.5 8.5q-0.297 0.297-0.703 0.297t-0.703-0.297-0.297-0.703v-4.5h-7q-0.406 0-0.703-0.297t-0.297-0.703v-6q0-0.406 0.297-0.703t0.703-0.297h7v-4.5q0-0.406 0.297-0.703t0.703-0.297 0.703 0.297l8.5 8.5q0.297 0.297 0.297 0.703z"></path></svg>' +
                '<span id="k-edit-bar__link--logout__text">Login</span>' +
                '</a>';
        }

        var elements = '' +
            '<a id="k-edit-bar__link--home" href="' + Nucleonplus.ToolBar.url.home + '">' +
            '<svg id="k-edit-bar__link--home__svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="28" height="28" viewBox="0 0 8 8"><path d="M4 0l-4 3h1v4h2v-2h2v2h2v-4.03l1 .03-4-3z"></path></svg>' +
            '<span id="k-edit-bar__link--home__text">Home</span>' +
            '</a>' +
            dashboardBtn +
            editBtn +
            newBtn +
            authBtn;
        koowaEditBar.id = 'k-edit-bar';
        document.body.appendChild(koowaEditBar);
        koowaEditBar.innerHTML = elements;

    });
})(document, window, domIsReady);
