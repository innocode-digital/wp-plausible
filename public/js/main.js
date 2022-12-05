(function (innstats, location, document) {

    'use strict';

    var utils = innstats.utils;
    var providers = innstats.providers;
    var referrer = document.referrer;

    var pageview = function (referrer, props) {
        for (var provider in providers) {
            if (utils.has(providers, provider)) {
                providers[provider].pageview(referrer, props);
            }
        }
    };

    var handlePageview = function () {
        if (referrer === location.href) {
            return;
        }

        pageview(referrer);

        referrer = location.href;
    };

    var handleQueriedObject = function () {
        if (referrer === location.href) {
            return;
        }

        var sanitizedReferrer = referrer.split('#')[0];
        var sanitizedHref = location.href.split('#')[0];

        if (sanitizedReferrer === sanitizedHref) {
            handlePageview();

            return;
        }

        var request = utils.xhr();
        var urlParts = sanitizedHref.split('?');
        var url = urlParts[0] + '?' + innstats.query_var + '=queried_object';

        if (urlParts[1]) {
            url += '&' + urlParts[1];
        }

        request.withCredentials = true;
        request.open('GET', url, true);
        request.setRequestHeader('Accept', 'application/json');
        request.onreadystatechange = (function (referrer) {
            return function () {
                if (request.readyState !== 4) {
                    return;
                }

                var response;

                try {
                    response = JSON.parse(request.responseText);
                } catch (e) {}

                pageview(referrer, response);
            };
        })(referrer);
        request.send();

        referrer = location.href;
    };

    pageview(referrer, innstats.queried_object || {});

    referrer = location.href;

    utils.addEventListener(window, 'hashchange', handlePageview);

    if (innstats.track_auto_pageviews) {
        if (innstats.queried_object) {
            utils.patchHistoryMethod('pushState', handleQueriedObject);
            utils.patchHistoryMethod('replaceState', handleQueriedObject);
            utils.addEventListener(window, 'popstate', handleQueriedObject);
        } else {
            utils.patchHistoryMethod('pushState', handlePageview);
            utils.patchHistoryMethod('replaceState', handlePageview);
            utils.addEventListener(window, 'popstate', handlePageview);
        }
    }

    innstats.pageview = pageview;
})(window.innstats, window.location, window.document);
