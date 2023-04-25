/* eslint-disable strict */
/* eslint-disable no-var */
/* eslint-disable prefer-destructuring */
/* eslint-disable object-shorthand */
/* eslint-disable no-restricted-syntax */
/* eslint-disable prefer-template */
/* eslint-disable no-param-reassign */
(function (innstats, location, document, navigator) {
  'use strict';

  var utils = innstats.utils;
  var timer;

  var pushEvent = function (name, referrer, props) {
    var url = utils.url('plausible', '/api/event');
    var payload = {
      domain: innstats.domain || 'Unknown',
      name: name,
      url: location.href,
      referrer: referrer,
      screen_width:
        window.innerWidth ||
        document.documentElement.clientWidth ||
        document.body.clientWidth,
      props: {
        screen_height:
          window.innerHeight ||
          document.documentElement.clientHeight ||
          document.body.clientHeight,
        device_pixel_ratio:
          typeof window.devicePixelRatio !== 'undefined'
            ? window.devicePixelRatio
            : 0,
        language:
          typeof navigator.language !== 'undefined' ? navigator.language : '' // eslint-disable-line prettier/prettier
      } // eslint-disable-line prettier/prettier
    };
    var prop;
    var request;
    var data;

    if (utils.has(innstats, 'ad_blocker')) {
      payload.props.ad_blocker = innstats.ad_blocker ? 'yes' : 'no';
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

    data = JSON.stringify(payload);

    if (navigator.sendBeacon) {
      navigator.sendBeacon(url, data);

      return;
    }

    request = utils.xhr();
    request.open('POST', url, true);
    request.setRequestHeader('Content-Type', 'application/json');
    request.send(data);
  };

  var pushQueriedObject = function (queriedObject, referrer, props) {
    var event = queriedObject.template;

    if (queriedObject.type) {
      event += ':' + queriedObject.type;
    }

    if (!props) {
      props = {};
    }

    props.id = queriedObject.id;

    pushEvent(event, referrer, props);
  };

  var pushAuthor = function (id, referrer, props) {
    if (!props) {
      props = {};
    }

    props.id = id;

    pushEvent('author', referrer, props);
  };

  var pageview = function (referrer, queriedObject, props) {
    pushEvent('pageview', referrer, props);

    if (timer) {
      clearTimeout(timer);
      timer = null;
    }

    timer = setTimeout(function () {
      if (queriedObject) {
        pushQueriedObject(queriedObject, referrer, props);

        if (utils.has(queriedObject, 'author') && queriedObject.author) {
          pushAuthor(queriedObject.author, referrer, props);
        }
      }

      timer = null;
    }, 15 * 1000); // Let's wait 15 seconds before sending the queried object and avoid unreal bounce rates.
  };

  innstats.providers.plausible.pushEvent = pushEvent;
  innstats.providers.plausible.pageview = pageview;
})(window.innstats, window.location, window.document, window.navigator);
