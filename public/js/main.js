/* eslint-disable strict */
/* eslint-disable no-var */
/* eslint-disable prefer-destructuring */
/* eslint-disable no-restricted-syntax */
/* eslint-disable prefer-template */
(function (innstats, location, document) {
  'use strict';

  var utils = innstats.utils;
  var providers = innstats.providers;
  var referrer = document.referrer;

  var pageview = function (ref, queriedObject, props) {
    var provider;

    for (provider in providers) {
      if (utils.has(providers, provider)) {
        providers[provider].pageview(ref, queriedObject, props);
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
    var sanitizedReferrer;
    var sanitizedHref;
    var request;
    var urlParts;
    var url;

    if (referrer === location.href) {
      return;
    }

    sanitizedReferrer = referrer.split('#')[0];
    sanitizedHref = location.href.split('#')[0];

    if (sanitizedReferrer === sanitizedHref) {
      handlePageview();

      return;
    }

    request = utils.xhr();
    urlParts = sanitizedHref.split('?');
    url = urlParts[0] + '?' + innstats.query_var + '=queried_object';

    if (urlParts[1]) {
      url += '&' + urlParts[1];
    }

    request.withCredentials = true;
    request.open('GET', url, true);
    request.setRequestHeader('Accept', 'application/json');
    request.onreadystatechange = (function (ref) {
      return function () {
        var response;

        if (request.readyState !== 4) {
          return;
        }

        try {
          response = JSON.parse(request.responseText);
        } catch (e) {
          // eslint-disable-next-line no-empty
        }

        pageview(ref, response);
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

  // eslint-disable-next-line no-param-reassign
  innstats.pageview = pageview;
})(window.innstats, window.location, window.document);
