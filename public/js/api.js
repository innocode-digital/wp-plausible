(function (innstats, wp) {
  const languageNames = new Intl.DisplayNames(
    [document.documentElement.lang || 'en-US'],
    { type: 'language' }
  );

  // eslint-disable-next-line no-param-reassign
  innstats.api = {
    request(endpoint, query) {
      let path = `/wpd/v1/statistics/${endpoint}`;

      const params = new URLSearchParams(query).toString();

      if (params) {
        path += `?${params}`;
      }

      return wp.apiRequest({ path });
    },

    realtimeVisitors(query) {
      return this.request('realtime_visitors', query);
    },

    aggregate(query) {
      return this.request('aggregate', query);
    },

    timeseries(query) {
      return this.request('timeseries', query);
    },

    breakdown(query) {
      return this.request('breakdown', query);
    },

    top(type, property, args = {}) {
      const query = {
        property: `${type}:${property}`,
        limit: 10,
        ...args,
      };

      query.limit += 1;

      return this.breakdown(query).then(
        (data) =>
          new Promise((resolve) => {
            resolve(
              data
                .filter((item) => item[property] !== 'Direct / None')
                .slice(0, query.limit - 1)
            );
          })
      );
    },

    event(property, query = {}) {
      return this.top('event', property, query);
    },

    visit(property, query = {}) {
      return this.top('visit', property, query);
    },

    custom(property, query = {}) {
      return this.top('event:props', property, query).then(
        (data) =>
          new Promise((resolve) => {
            resolve(
              data.map((item) => {
                let value = item.props[property];

                if (property === 'language') {
                  value = `${languageNames.of(value)} (${value})`;
                }

                return {
                  ...item,
                  [property]: value,
                };
              })
            );
          })
      );
    },
  };
})(window.innstats, window.wp);
