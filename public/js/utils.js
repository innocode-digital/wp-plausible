/* eslint-disable strict */
/* eslint-disable object-shorthand */
/* eslint-disable no-var */
/* eslint-disable prefer-template */
/* global ActiveXObject */
(function (innstats, location, document, navigator, history) {
  'use strict';

  // Yep, old-school - ES3 with even XMLHttpRequest for better cross browser support.

  // eslint-disable-next-line no-param-reassign
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
      var original;

      if (!history) {
        return;
      }

      original = history[method];

      if (!original) {
        return;
      }

      // eslint-disable-next-line no-param-reassign
      history[method] = function patchHistoryMethod() {
        // eslint-disable-next-line prefer-rest-params
        const result = original.apply(this, arguments);

        fn();

        return result;
      };
    },
    url: function (provider, path) {
      return innstats.providers[provider].api_root.replace(/\/+$/, '') + path;
    },
    xhr: function () {
      return window.XMLHttpRequest
        ? new XMLHttpRequest()
        : new ActiveXObject('Microsoft.XMLHTTP');
    },
  };
})(
  window.innstats,
  window.location,
  window.document,
  window.navigator,
  window.history
);
