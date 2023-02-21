(function (innstats, wp, ChartGeo, $) {
  wp.domReady(() => {
    const { api, charts } = innstats;

    const number2human = (number = 0) =>
      new Intl.NumberFormat(document.documentElement.lang || 'en-US').format(
        number
      );

    const date2timeseries = (date) =>
      new Date(date).toLocaleDateString(
        document.documentElement.lang || 'en-US',
        {
          month: 'short',
          day: 'numeric',
          year: 'numeric',
        }
      );

    const seconds2human = (seconds = 0) => {
      const datetime = new Date(seconds * 1000).toISOString();

      return seconds < 3600
        ? datetime.substring(14, 19)
        : datetime.substring(11, 16);
    };

    const showSpinner = (el) => {
      const spinner = document.createElement('span');

      spinner.classList.add('spinner');
      el.appendChild(spinner);
    };

    const hideSpinner = (widget) => {
      const el = document.getElementById(`innstats-widget-${widget}`);
      const spinner = el.nextElementSibling;

      if (spinner) {
        spinner.remove();
      }
    };

    const showSpinners = (section) => {
      const el = document.getElementById(`innstats-section-${section}`);
      const insides = el.querySelectorAll('.innstats-widget > .inside');

      [...insides].forEach((inside) => {
        showSpinner(inside);
      });
    };

    const observe = (section, fn) => {
      const el = document.getElementById(`innstats-section-${section}`);

      if (!el) {
        return;
      }

      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) {
            return;
          }

          fn(el);
          observer.unobserve(el);
        });
      });

      observer.observe(el);
    };

    observe('general', () => {
      showSpinners('general');

      const misc = document.getElementById('innstats-widget-misc');
      const realtimeVisitors = document.createElement('div');
      const aggregate = document.createElement('dl');
      const topCountries = document.createElement('div');

      realtimeVisitors.classList.add(
        'innstats-widget',
        'innstats-widget_realtime-visitors'
      );
      aggregate.classList.add('innstats-widget', 'innstats-widget_aggregate');
      topCountries.classList.add(
        'innstats-widget',
        'innstats-widget_top-countries'
      );

      misc.appendChild(aggregate);
      misc.appendChild(realtimeVisitors);
      misc.appendChild(topCountries);

      api.realtimeVisitors().then((data) => {
        realtimeVisitors.innerHTML = `<span class="innstats-realtime-status innstats-realtime-status_online" aria-label="Online"></span> <span id="innstats-realtime-visitors">${data}</span> currently online`;
        hideSpinner('misc');
      });

      api
        .aggregate({
          metrics: ['visitors', 'pageviews', 'bounce_rate', 'visit_duration'],
          compare: 'previous_period',
        })
        .then((data) => {
          [
            ['visitors', 'Unique visitors'],
            ['pageviews', 'Total pageviews'],
            ['visit_duration', 'Visit duration'],
            ['bounce_rate', 'Bounce rate'],
          ].forEach(([key, title]) => {
            const { value, change } = data[key];

            const dt = document.createElement('dt');

            dt.innerHTML = title;
            aggregate.appendChild(dt);

            const dd = document.createElement('dd');

            const display = `<span id="innstats-${key}">${
              key === 'visit_duration'
                ? seconds2human(value)
                : number2human(value)
            }</span>`;
            const unit = key === 'bounce_rate' ? '%' : '';
            const displayChange = change
              ? ` <span id="innstats-${key}-change" class="innstats-badge innstats-badge_${
                  change < 0 ? 'danger' : 'success'
                }">${change}%</span>`
              : '';

            dd.innerHTML = `${display}${unit}${displayChange}`;
            aggregate.appendChild(dd);
          });

          hideSpinner('misc');
        });

      api
        .timeseries({
          metrics: ['visitors', 'pageviews', 'bounce_rate', 'visit_duration'],
        })
        .then((data) => {
          charts.lines(
            data.map((item) => ({
              ...item,
              timeseries: date2timeseries(item.date),
            })),
            'timeseries',
            null,
            ['visitors', 'pageviews'],
            {
              visitors: 'Unique visitors',
              pageviews: 'Total pageviews',
            }
          );
          charts.line(
            data.map((item) => ({
              ...item,
              y: item.visit_duration,
              value: seconds2human(item.visit_duration),
              visit_duration: date2timeseries(item.date),
            })),
            'visit_duration',
            'Visit duration',
            (value) => seconds2human(value)
          );
          charts.line(
            data.map((item) => ({
              ...item,
              y: item.bounce_rate,
              value: `${item.bounce_rate}%`,
              bounce_rate: date2timeseries(item.date),
            })),
            'bounce_rate',
            'Bounce rate',
            (value) => `${value}%`
          );
          hideSpinner('timeseries');
          hideSpinner('visit_duration');
          hideSpinner('bounce_rate');
        });

      Promise.all([
        fetch(
          'https://cdn.jsdelivr.net/npm/visionscarto-world-atlas@0.1.0/world/110m.json'
        ).then((response) => response.json()),
        fetch(
          'https://cdn.jsdelivr.net/npm/i18n-iso-countries@7.5.0/codes.json'
        ).then((response) => response.json()),
        api.visit('country', { limit: 100 }),
      ]).then(([countries, codes, data]) => {
        const features = ChartGeo.topojson
          .feature(countries, countries.objects.countries)
          .features.filter(
            (feature) => feature.properties.name !== 'Antarctica'
          );

        charts.choropleth(
          features.map((feature) => ({
            feature,
            country: feature.properties.name,
            visitors: data.find(
              (item) =>
                feature.id ===
                codes.find(([code]) => code === item.country)?.[2]
            )?.visitors,
          })),
          'country',
          'Countries'
        );
        hideSpinner('country');

        topCountries.innerHTML = '<h4>Top Countries</h4><ul></ul>';

        data.slice(0, 10).forEach(({ country, visitors }) => {
          const li = document.createElement('li');
          const flag = String.fromCodePoint(
            ...country
              .toUpperCase()
              .split('')
              .map((char) => 127397 + char.charCodeAt(0))
          );
          const name =
            features.find(
              (feature) =>
                feature.id === codes.find(([code]) => code === country)?.[2]
            )?.properties.name || country;

          li.innerHTML = `${flag} ${name} <strong>${number2human(
            visitors
          )}</strong>`;
          topCountries.querySelector('ul').appendChild(li);
          hideSpinner('misc');
        });
      });
    });

    observe('top_pages', () => {
      showSpinners('top_pages');

      api.event('page').then((data) => {
        charts.horizontalBar(data, 'page', 'Popular');
        hideSpinner('page');
      });

      const horizontalBars = {
        entry_page: 'Entry Pages',
        exit_page: 'Exit Pages',
      };

      Object.keys(horizontalBars).forEach((key) => {
        api.visit(key).then((data) => {
          charts.horizontalBar(data, key, horizontalBars[key]);
          hideSpinner(key);
        });
      });
    });

    observe('top_sources', () => {
      showSpinners('top_sources');

      const horizontalBars = {
        source: 'Popular',
        utm_medium: 'UTM Medium',
        utm_source: 'UTM Source',
        utm_campaign: 'UTM Campaign',
        utm_term: 'UTM Term',
        utm_content: 'UTM Content',
      };

      Object.keys(horizontalBars).forEach((key) => {
        api.visit(key).then((data) => {
          charts.horizontalBar(data, key, horizontalBars[key]);
          hideSpinner(key);
        });
      });
    });

    observe('devices_and_browsers', () => {
      showSpinners('devices_and_browsers');

      const pies = {
        device: 'Devices',
        browser: 'Browsers',
        os: 'Operating Systems',
        language: 'Languages',
      };

      Object.keys(pies).forEach((key) => {
        const deferred = ['device', 'browser', 'os'].includes(key)
          ? api.visit(key)
          : api.custom(key);

        deferred.then((data) => {
          charts.pie(data, key, pies[key]);
          hideSpinner(key);
        });
      });

      api.custom('device_pixel_ratio').then((data) => {
        const sorted = data.sort(
          (a, b) => a.device_pixel_ratio - b.device_pixel_ratio
        );

        charts.bar(
          sorted,
          'device_pixel_ratio',
          'Retina',
          'Device Pixel Ratio'
        );
        hideSpinner('device_pixel_ratio');
      });

      api
        .custom('ad_blocker', {
          limit: 2, // Only 'yes' and 'no'
        })
        .then((data) => {
          charts.bar(data.reverse(), 'ad_blocker', 'Ad Blocker');
          hideSpinner('ad_blocker');
        });
    });

    if ($) {
      $(document).on('heartbeat-send', () => {
        api.realtimeVisitors().then((data) => {
          const el = document.getElementById('innstats-realtime-visitors');

          el.innerHTML = data;
        });

        api
          .aggregate({
            metrics: ['visitors', 'pageviews', 'bounce_rate', 'visit_duration'],
            compare: 'previous_period',
          })
          .then((data) => {
            Object.keys(data).forEach((key) => {
              const { value, change } = data[key];
              const el = document.getElementById(`innstats-${key}`);
              const changeEl = document.getElementById(
                `innstats-${key}-change`
              );

              if (el) {
                el.innerHTML =
                  key === 'visit_duration'
                    ? seconds2human(value)
                    : number2human(value);
              }

              if (change && changeEl) {
                changeEl.innerHTML = `${change > 0 ? '+' : ''}${change}%`;
                changeEl.classList.remove(
                  'innstats-badge_success',
                  'innstats-badge_danger'
                );
                changeEl.classList.add(
                  `innstats-badge_${change > 0 ? 'success' : 'danger'}`
                );
              }
            });
          });
      });
    }
  });
})(window.innstats, window.wp, window.ChartGeo, window.jQuery);
