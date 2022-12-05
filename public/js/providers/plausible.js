(function (innstats, location, document, navigator) {

    'use strict';

    var utils = innstats.utils;

    var pushEvent = function (name, referrer, props) {
        var payload = {
            domain: innstats.domain || 'Unknown',
            name: name,
            url: location.href,
            referrer: referrer,
            screen_width: window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
            props: {
                screen_height: window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
                device_pixel_ratio: typeof window.devicePixelRatio !== 'undefined' ? window.devicePixelRatio : 0,
                language: typeof navigator.language !== 'undefined' ? navigator.language : ''
            }
        };
        var prop;
        var request;

        if (utils.has(innstats, 'ad_blocker')) {
            payload.props.ad_blocker = innstats.ad_blocker;
        }

        if (innstats.props && !utils.isArray(innstats.props)) {
            for (prop in innstats.props) {
                if (utils.has(innstats.props, prop)) {
                    payload.props[prop] = innstats.props[prop];
                }
            }
        }

        if (props) {
            for (prop in props) {
                if (utils.has(props, prop)) {
                    payload.props[prop] = props[prop];
                }
            }
        }

        request = utils.xhr();
        request.open('POST', utils.url('plausible', '/api/event'), true);
        request.setRequestHeader('Content-Type', 'application/json');
        request.send(JSON.stringify(payload));
    };

    var pageview = function (referrer, props) {
        pushEvent('pageview', referrer, props);
    };

    innstats.providers.plausible.pushEvent = pushEvent;
    innstats.providers.plausible.pageview = pageview;
})(window.innstats, window.location, window.document, window.navigator);
