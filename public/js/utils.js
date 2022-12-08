(function (innstats, location, document, navigator, history) {

  // Yep, old-school - ES3 with even XMLHttpRequest for better cross browser support.

  'use strict';

  innstats.utils = {
    has: function (obj, prop) {
      return Object.prototype.hasOwnProperty.call(obj, prop);
    },
    isArray: function (value) {
      return Object.prototype.toString.call(value) !== '[object Array]';
    },
    addEventListener: function (el, type, fn) {
      if (el.addEventListener) {
        el.addEventListener(type, fn, false);
      } else if (el.attachEvent) {
        el.attachEvent('on' + type, fn);
      }

      return el;
    },
    patchHistoryMethod: function (method, fn) {
      if (!history) {
        return;
      }

      var original = history[method];

      if (!original) {
        return;
      }

      history[method] = function () {
        var result = original.apply(this, arguments);

        fn();

        return result;
      };
    },
    url: function (provider, path) {
      return innstats.providers[provider].api_root.replace(/\/+$/, '') + path;
    },
    xhr: function () {
      return window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    }
  };
})(window.innstats, window.location, window.document, window.navigator, window.history);
