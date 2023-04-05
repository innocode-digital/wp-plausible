(function (innstats, wp) {
  // eslint-disable-next-line no-param-reassign
  innstats.api = {
    request(endpoint, query = {}) {
      this.endpoint = endpoint;
      this.query = query;
      this.period = '30d';
      this.startDate = null;
      this.endDate = null;
      this.metrics = ['visitors'];
      this.compare = null;
      this.filters = [];
      this.limit = null;
      this.page = null;

      return this;
    },

    byTimePeriod(period, start = null, end = null) {
      this.period = period;

      if (period === 'custom') {
        if (start === null || end === null) {
          throw new Error('Start and end dates are required for custom period');
        }

        this.startDate = start;
        this.endDate = end;
      }

      return this;
    },

    useMetrics(metrics) {
      this.metrics = metrics;

      return this;
    },

    addMetric(metric) {
      this.metrics.push(metric);

      return this;
    },

    withPreviousPeriod() {
      this.compare = 'previous_period';

      return this;
    },

    useFilters(filters) {
      this.filters = filters;

      return this;
    },

    filterBy(property, value, operator = '==') {
      this.filters.push(`${property}${operator}${value}`);

      return this;
    },

    filterByEvent(property, value, operator = '==') {
      return this.filterBy(`event:${property}`, value, operator);
    },

    filterByVisit(property, value, operator = '==') {
      return this.filterBy(`visit:${property}`, value, operator);
    },

    limitResults(limit = 10) {
      this.limit = limit;

      return this;
    },

    paginate(page) {
      this.page = page;

      return this;
    },

    send() {
      let path = `/wpd/v1/statistics/${this.endpoint}`;

      const params = new URLSearchParams({
        ...this.query,
        period: this.period,
        ...(this.period === 'custom' && {
          date: `${this.startDate},${this.endDate}`,
        }),
        metrics: this.metrics.join(','),
        ...(this.compare && { compare: this.compare }),
        ...(this.filters.length && { filters: this.filters.join(';') }),
        ...(this.limit && { limit: this.limit }),
        ...(this.page && { page: this.page }),
      }).toString();

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

    breakdown(property, query) {
      return this.request('breakdown', {
        property,
        ...query,
      });
    },

    breakdownByEvent(property, query) {
      return this.breakdown(`event:${property}`, query);
    },

    breakdownByVisit(property, query) {
      return this.breakdown(`visit:${property}`, query);
    },

    breakdownByProp(property, query) {
      return this.breakdown(`event:props:${property}`, query);
    },
  };
})(window.innstats, window.wp);
