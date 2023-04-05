(function (innstats, wp, $) {
  wp.domReady(() => {
    const { per_page: perPage } = innstats;
    const $form = $('#innstats-table-goals');
    const $paged = $form.find('input[name="paged"]');
    const $list = $('#the-list').wpList({
      alt: '',
      delColor: 'none',
      addColor: 'none',
    });
    const $loader = $list.find('tr').first();
    const $loadMore = $('#innstats-load-more-goals');

    const request = (args) =>
      $.ajax({
        url: window.ajaxurl,
        global: false,
        dataType: 'json',
        data: {
          list_args: {
            ...window.list_args,
            class: window.list_args.class.replace(/\\/g, ''), // Remove backslashes from namespace
          },
          number: perPage,
          ...$form.serializeArray().reduce(
            (data, { name, value }) => ({
              ...data,
              [name]: value,
            }),
            {}
          ),
          ...args,
        },
        success({ rows }) {
          const $rows = $(rows);

          if (rows) {
            $list.get(0).wpList.add(rows);
          }

          $loader.hide().appendTo($list);

          if ($rows.length < perPage) {
            $loadMore.hide();
          } else {
            $paged.val(Number.parseInt($paged.val(), 10) + 1);
            $loadMore.show();
          }
        },
      });

    request();

    $form.on('submit', (event) => {
      event.preventDefault();
      $list.find('tr').not($loader).empty();
      $loader.show();
      $paged.val(1);
      request();
    });
    $loadMore.on('click', (event) => {
      event.preventDefault();
      $loader.show();
      request({
        no_placeholder: true,
      });
    });
  });
})(window.innstats, window.wp, window.jQuery);
