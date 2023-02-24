/* eslint-disable no-param-reassign */
(function (innstats, Chart) {
  Chart.defaults.color = '#1d2327';
  Chart.defaults.font.family =
    '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif';

  innstats.charts = {
    config(data, property, title) {
      return {
        data: {
          labels: data.map((item) => item[property]),
        },
        options: {
          plugins: {
            title: {
              align: 'start',
              display: !!title,
              font: {
                size: 14,
                weight: 600,
              },
              text: title,
            },
          },
          onClick: (event, active, chart) => {
            if (!active.length) {
              return;
            }

            const { url } = chart.data.datasets[0].data[active[0].index];

            if (typeof url !== 'undefined' && url !== null) {
              window.open(url, '_blank').focus();
            }
          },
        },
      };
    },

    render(widget, config) {
      // eslint-disable-next-line no-new
      return new Chart(
        document.getElementById(`innstats-widget-${widget}`),
        config
      );
    },

    horizontalBar(data, property, title) {
      const config = this.config(data, property, title);

      return this.render(property, {
        ...config,
        type: 'bar',
        data: {
          ...config.data,
          datasets: [
            {
              data: data.map((item) => ({
                x: item.visitors,
                y: item[property],
                url: property.includes('page')
                  ? `${innstats.home_url}${item[property]}`
                  : null,
              })),
              barPercentage: 1,
              categoryPercentage: 1,
              minBarLength: 4,
            },
          ],
        },
        options: {
          ...config.options,
          aspectRatio: 1.25,
          indexAxis: 'y',
          plugins: {
            ...config.options.plugins,
            legend: {
              display: false,
            },
          },
          scales: {
            x: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Visitors',
              },
            },
            y: {
              grid: {
                display: false,
              },
              ticks: {
                mirror: true,
              },
            },
          },
        },
      });
    },

    bar(data, property, title, subtitle = null) {
      const config = this.config(data, property, title);

      return this.render(property, {
        ...config,
        type: 'bar',
        data: {
          ...config.data,
          datasets: [
            {
              data: data.map((item) => ({
                x: item[property],
                y: item.visitors,
              })),
              minBarLength: 4,
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(201, 203, 207, 0.2)',
              ],
              borderColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
              ],
              borderWidth: 1,
            },
          ],
        },
        options: {
          ...config.options,
          aspectRatio: 1,
          plugins: {
            ...config.options.plugins,
            legend: {
              display: false,
            },
          },
          scales: {
            x: {
              beginAtZero: true,
              grid: {
                display: false,
              },
              title: {
                display: !!subtitle,
                text: subtitle,
              },
            },
            y: {
              title: {
                display: true,
                text: 'Visitors',
              },
            },
          },
        },
      });
    },

    pie(data, property, title) {
      const config = this.config(data, property, title);

      return this.render(property, {
        ...config,
        type: 'pie',
        data: {
          ...config.data,
          datasets: [
            {
              data: data.map((item) => ({
                key: item[property],
                value: item.visitors,
              })),
            },
          ],
        },
      });
    },

    choropleth(data, property, title) {
      const config = this.config(data, property, title);

      return this.render(property, {
        ...config,
        type: 'choropleth',
        data: {
          ...config.data,
          datasets: [
            {
              data: data.map((item) => ({
                feature: item.feature,
                value: item.visitors,
              })),
              outline: data.map((item) => item.feature),
            },
          ],
        },
        options: {
          ...config.options,
          aspectRatio: 1.25,
          plugins: {
            ...config.options.plugins,
            legend: {
              display: false,
            },
            tooltip: {
              callbacks: {
                label(context) {
                  let label = context.raw.feature.properties.name || '';

                  if (label) {
                    label += ': ';
                  }

                  if (
                    context.parsed.r !== null &&
                    !Number.isNaN(context.parsed.r)
                  ) {
                    label += context.parsed.r;
                  } else {
                    label += 'No data';
                  }

                  return label;
                },
              },
            },
          },
          scales: {
            color: {
              axis: 'xy',
              interpolate: 'oranges',
              legend: {
                position: 'bottom-left',
              },
            },
            projection: {
              axis: 'x',
              projection: 'mercator',
            },
          },
        },
      });
    },

    line(data, property, title, tickFormat) {
      const config = this.config(data, property, title);

      return this.render(property, {
        ...config,
        type: 'line',
        data: {
          ...config.data,
          datasets: [
            {
              borderColor: 'rgb(255, 159, 64)',
              data: data.map(({ y, value }, index) => ({
                x: index,
                y,
                value,
              })),
              fill: true,
            },
          ],
        },
        options: {
          ...config.options,
          plugins: {
            ...config.options.plugins,
            legend: {
              display: false,
            },
            tooltip: {
              callbacks: {
                label(context) {
                  let label = context.dataset.label || '';

                  if (label) {
                    label += ': ';
                  }

                  if (context.raw.value !== null) {
                    label += context.raw.value;
                  } else if (context.parsed.y !== null) {
                    label += context.parsed.y;
                  } else {
                    label += 'No data';
                  }

                  return label;
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback(value) {
                  return tickFormat(value);
                },
                stepSize: 1,
              },
            },
          },
        },
      });
    },

    lines(data, root, title, properties, titles = {}) {
      const config = this.config(data, root, title);

      return this.render(root, {
        ...config,
        type: 'line',
        data: {
          ...config.data,
          datasets: properties.map((property) => ({
            label: titles[property],
            data: data.map((item) => item[property]),
            fill: true,
          })),
        },
        options: {
          ...config.options,
          plugins: {
            ...config.options.plugins,
            legend: {
              labels: {
                boxWidth: Chart.defaults.font.size,
                boxHeight: Chart.defaults.font.size,
                font: {
                  weight: 600,
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      });
    },
  };
})(window.innstats, window.Chart);
