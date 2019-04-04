<?php

declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'Редиректы',
        'description' => 'Удобное управление редиректами',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Редиректы',
            'tab' => 'Редиректы',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Редиректы',
        'menu_description' => 'Управление редиректами',
    ],
    'settings' => [
        'menu_label' => 'Redirects', // TODO
        'menu_description' => 'Manage settings for Redirects.', // TODO
        'logging_enabled_label' => 'Log redirect events', // TODO
        'logging_enabled_comment' => 'Store redirect events in the database.', // TODO
        'statistics_enabled_label' => 'Gather statistics', // TODO
        'statistics_enabled_comment' => 'Gather statistics of redirected requests to get more insight.', // TODO
        'test_lab_enabled_label' => 'TestLab (beta)', // TODO
        'test_lab_enabled_comment' => 'TestLab allows you to mass test your redirects.', // TODO
        'caching_enabled_label' => 'Caching of redirects (advanced)', // TODO
        'caching_enabled_comment' => 'Improves the redirect engine when having a lot of redirects. ' // TODO
            . 'CAUTION: Cache driver `file` and `database` are NOT supported. '
            . 'Recommended driver is `memcached` or a similar "in-memory" caching driver.',
    ],
    'redirect' => [
        'redirect' => 'Редиректы',
        'from_url' => 'Исходный путь',
        'from_url_placeholder' => '/source/path',
        'from_url_comment' => 'Исходный путь относительно корня сайта.',
        'from_scheme' => 'Source scheme', // TODO
        'from_scheme_comment' => 'Force match on scheme. If HTTP is selected <u>http://domain.com/path</u> will '
            . 'match and <u>https://domain.com/path</u> does not match.', // TODO
        'to_url' => 'Путь редиректа или URL',
        'to_url_placeholder' => '/absolute/path, relative/path или http://target.url',
        'to_url_comment' => 'Абсолютный путь, относительный путь или URL для перенаправления.',
        'to_url_required_if' => 'Исходный путь или URL обязателен для заполнения',
        'to_scheme' => 'Target scheme', // TODO
        'to_scheme_comment' => 'Target scheme will be forced to HTTP or HTTPS '
            . 'or choose AUTOMATIC to use the default scheme of the website.', // TODO
        'scheme_auto' => 'Automatic', // TODO
        'cms_page_required_if' => 'Пожалуйста, выберите страницу CMS для перенаправления',
        'static_page_required_if' => 'Пожалуйста, пропишите статическую страницу для перенаправления',
        'match_type' => 'Тип соответствия',
        'exact' => 'Точный',
        'placeholders' => 'По меткам',
        'regex' => 'Regular expression', // TODO
        'target_type' => 'Тип цели редиректа',
        'target_type_none' => 'Not applicable', // TODO
        'target_type_path_or_url' => 'Путь или URL',
        'target_type_cms_page' => 'Страница CMS',
        'target_type_static_page' => 'Статическая страница',
        'status_code' => 'Код HTTP-статуса',
        'sort_order' => 'Порядок сортировки',
        'requirements' => 'Параметры меток',
        'requirements_comment' => 'Добавьте параметры для каждого условия.',
        'placeholder' => 'Метка',
        'placeholder_comment' => 'Имя метки (включая фигурные скобки) проставленной в поле \'Исходный путь\'. Например, {category} или {id}',
        'requirement' => 'Параметры',
        'requirement_comment' => 'Пропишите параметры с помощью регулярных выражений. Например, [0-9]+ или [a-zA-Z]+.',
        'requirements_prompt' => 'Добавить новый параметр',
        'replacement' => 'Замена',
        'replacement_comment' => 'Пропишите (опционально) замену для текущей метки. В целевом URL метка будет заменена на это значение.',
        'permanent' => '301 - перемещено навсегда',
        'temporary' => '302 - перемещено временно',
        'see_other' => '303 - смотреть другое',
        'not_found' => '404 - не найдено',
        'gone' => '410 - удалено',
        'enabled' => 'Включено',
        'none' => 'none', // TODO
        'enabled_comment' => 'Сдвиньте переключатель для включения этого редиректа.',
        'priority' => 'Приоритет',
        'hits' => 'Переходы',
        'return_to_redirects' => 'Вернуться к списку редиректов',
        'return_to_categories' => 'Вернуться к списку категорий',
        'delete_confirm' => 'Вы уверены?',
        'created_at' => 'Создано в',
        'modified_at' => 'Изменено в',
        'system_tip' => 'Системный редирект',
        'user_tip' => 'Пользовательский редирект',
        'type' => 'Тип',
        'last_used_at' => 'Последнее использование',
        'and_delete_log_item' => 'И удалить выбранные элементы лога',
        'category' => 'Категория',
        'categories' => 'Категории',
        'description' => 'Description', // TODO
        'name' => 'Имя',
        'date_time' => 'Дата и время',
        'date' => 'Дата',
        'truncate_confirm' => 'Вы уверены, что хотите удалить ВСЕ записи?',
        'truncating' => 'Удаление...',
        'warning' => 'Warning', // TODO
        'cache_warning' => 'You have enabled caching but your caching driver is not supported. ' // TODO
            . 'Redirects will not be cached.',
        'general_confirm' => 'Are you sure you want to do this?', // TODO
        'sparkline_30d' => 'Hits (30d)', // TODO
        'has_hits' => 'Has hits', // TODO
        'minimum_hits' => 'Minimum # hits', // TODO
        'invalid_regex' => 'Invalid regular expression.', // TODO
    ],
    'list' => [
        'no_records' => 'В этом списке нет редиректов.',
        'switch_is_enabled' => 'Включено',
        'switch_system' => 'Системные редиректы',
    ],
    'scheduling' => [
        'from_date' => 'Дата включения',
        'from_date_comment' => 'Дата, с которой редирект будет активен. Не обязательное поле.',
        'to_date' => 'Дата выключения',
        'to_date_comment' => 'Дата, по которую редирект будет активен. Не обязательное поле.',
        'scheduling_comment' => 'Здесь вы можете задать период, в течении которого редирект будет активен. Возможны любые комбинации дат.',
        'not_active_warning' => 'Redirect is not available anymore, please check \'Scheduling\' tab.', // TODO
    ],
    'test' => [
        'test_comment' => 'Пожалуйста, проверьте редирект перед сохранением.',
        'input_path' => 'Введите путь',
        'input_path_comment' => 'Путь для тестирование. Например, /old-blog/category/123',
        'input_path_placeholder' => '/input/path',
        'input_scheme' => 'Input scheme', // TODO
        'test_date' => 'Выберите дату',
        'test_date_comment' => 'Если вы запланировали редирект по расписанию, вы можете проверить его работу для конкретной даты.',
        'testing' => 'Проверка...',
        'run_test' => 'Запустить проверку',
        'no_match_label' => 'Извините, совпадения не найдены!',
        'no_match' => 'Совпадений не найдено!',
        'match_success_label' => 'Есть совпадение!',
    ],
    'test_lab' => [
        'section_test_lab_comment' => 'TestLab allows you to mass test your redirects.', // TODO
        'test_lab_label' => 'Include in TestLab', // TODO
        'test_lab_enable' => 'Flick this switch to allow testing this redirect in the TestLab.', // TODO
        'test_lab_path_label' => 'Test Path', // TODO
        'test_lab_path_comment' => 'This path will be used when performing tests. '
            . 'Replace placeholders with real values.', // TODO
        'start_tests' => 'Start Tests', // TODO
        'start_tests_description' => 'Press the \'Start tests\' button to begin.', // TODO
        'edit' => 'Edit', // TODO
        'exclude' => 'Exclude', // TODO
        'exclude_confirm' => 'Are you sure want to exclude this redirect from TestLab?', // TODO
        'exclude_indicator' => 'Excluding redirect from TestLab', // TODO
        're_run' => 'Re-run', // TODO
        're_run_indicator' => 'Running tests, please wait...', // TODO
        'loop' => 'Loop', // TODO
        'match' => 'Match', // TODO
        'response_http_code' => 'Response HTTP code', // TODO
        'response_http_code_should_be' => 'Response HTTP code should be one of:', // TODO
        'redirect_count' => 'Redirect count', // TODO
        'final_destination' => 'Final Destination', // TODO
        'no_redirects' => 'No redirects have been marked with TestLab enabled. '
            . 'Please enable the option \'Include in TestLab\' when editing a redirect.', // TODO
        'test_error' => 'An error occurred when testing this redirect.', // TODO
        'flash_test_executed' => 'Test has been executed.', // TODO
        'flash_redirect_excluded' => 'Redirect has been excluded from TestLab and will not show up on next test run.', // TODO
        'result_request_failed' => 'Could not execute request.', // TODO
        'redirects_followed' => 'Number of redirects followed: :count (limited to :limit)', // TODO
        'not_determinate_destination_url' => 'Could not determine final destination URL.', // TODO
        'no_destination_url' => 'No final destination URL.', // TODO
        'final_destination_is' => 'Final destination is: :destination', // TODO
        'possible_loop' => 'Possible redirect loop!', // TODO
        'no_loop' => 'No redirect loop detected.', // TODO
        'not_match_redirect' => 'Did not match any redirect.', // TODO
        'matched' => 'Matched', // TODO
        'redirect' => 'redirect', // TODO
        'matched_not_http_code' => 'Matched redirect, but response HTTP code did not match! '
            . 'Expected :expected but received :received.', // TODO
        'matched_http_code' => 'Matched redirect, response HTTP code :code.', // TODO
        'executing_tests' => 'Executing tests...', // TODO
    ],
    'statistics' => [
        'hits_per_day' => 'Redirect hits per day', // TODO
        'click_on_chart' => 'Click on the chart to enable zooming and dragging.', // TODO
        'requests_redirected' => 'Requests redirected', // TODO
        'all_time' => 'all time', // TODO
        'active_redirects' => 'Active redirects', // TODO
        'redirects_this_month' => 'Redirects this month', // TODO
        'previous_month' => 'previous month', // TODO
        'latest_redirected_requests' => 'Latest redirected request', // TODO
        'redirects_per_month' => 'Redirects per month', // TODO
        'no_data' => 'No data', // TODO
        'top_crawlers_this_month' => 'Top :top crawlers this month', // TODO
        'top_redirects_this_month' => 'Top :top redirects this month', // TODO
    ],
    'title' => [
        'import' => 'Импорт',
        'export' => 'Экспорт',
        'redirects' => 'Управление редиректами',
        'create_redirect' => 'Создать редирект',
        'edit_redirect' => 'Редактировать редирект',
        'categories' => 'Управление категориями',
        'create_category' => 'Создать категорию',
        'edit_category' => 'Редактировать категорию',
        'view_redirect_log' => 'Смотреть лог редиректов',
        'statistics' => 'Статистика',
        'test_lab' => 'TestLab (beta)', // TODO
    ],
    'buttons' => [
        'add' => 'Добавить',
        'from_request_log' => 'Из лога запросов',
        'new_redirect' => 'Новый редирект',
        'create_redirects' => 'Создание редиректов',
        'create_and_new' => 'Create and new', // TODO
        'delete' => 'Удалить',
        'enable' => 'Включить',
        'disable' => 'Отключить',
        'reorder_redirects' => 'Упорядочить',
        'export' => 'Экспортировать',
        'import' => 'Импортировать',
        'categories' => 'Категории',
        'new_category' => 'Новая категория',
        'reset_statistics' => 'Сбросить статистику',
        'logs' => 'Лог редиректов',
        'empty_redirect_log' => 'Очистить лог',
        'clear_cache' => 'Clear cache', // TODO
        'stop' => 'Stop', // TODO
    ],
    'tab' => [
        'tab_general' => 'Основные',
        'tab_requirements' => 'Параметры меток',
        'tab_test' => 'Проверка',
        'tab_scheduling' => 'Расписание',
        'tab_test_lab' => 'TestLab', // TODO
        'tab_advanced' => 'Advanced', // TODO
    ],
    'flash' => [
        'success_created_redirects' => 'Успешно создано :count редирект(ов)',
        'static_page_redirect_not_supported' => 'Этот редирект нельзя изменить. Необходим плагин RainLab.Pages.',
        'truncate_success' => 'Все записи успешно удалены',
        'delete_selected_success' => 'Выбранные записи успешно удалены',
        'cache_cleared_success' => 'Successfully cleared redirect cache', // TODO
    ],
    'import_export' => [ // TODO
        'match_type' => 'Match Type [match_type] (Allowed values: exact, placeholders, regex)',
        'category_id' => 'Category [category_id]',
        'target_type' => 'Target Type [target_type] (Allowed values: path_or_url, cms_page, static_page, none)',
        'from_url' => 'Source path [from_url]',
        'from_scheme' => 'Source scheme [from_scheme] (Allowed values: http, https, auto [default])',
        'to_url' => 'Target path [to_url]',
        'to_scheme' => 'Target scheme [to_scheme] (Allowed values: http, https, auto [default])',
        'test_url' => 'Test URL [test_url]',
        'cms_page' => 'CMS Page [cms_page] (Filename without .htm extension)',
        'static_page' => 'Static Page [static_page] (Filename without .htm extension)',
        'requirements' => 'Placeholder requirements [requirements]',
        'status_code' => 'HTTP status code [status_code] (Possible values: 301, 302, 303, 404, 410)',
        'hits' => 'Redirect Hits [hits]',
        'from_date' => 'Scheduled date from [from_date] (YYYY-MM-DD or empty)',
        'to_date' => 'Scheduled date to [to_date] (YYYY-MM-DD or empty)',
        'sort_order' => 'Priority [sort_order]',
        'is_enabled' => 'Enabled [is_enabled] (1 = enable redirect, 0 = disable redirect [default])',
        'test_lab' => 'Test Lab [test_lab] (1 = enable Test Lab, 0 = disable TestLab [default])',
        'test_lab_path' => 'Test Lab path [test_lab_path] (required if match_type = placeholders)',
        'system' => 'System [system] (1 = system generated redirect, 0 = user generated redirect [default])',
        'description' => 'Description [description]',
        'last_used_at' => 'Last Used At [last_used_at] (YYYY-MM-DD HH:MM:SS or empty)',
        'created_at' => 'Created At [created_at] (YYYY-MM-DD HH:MM:SS or empty)',
        'updated_at' => 'Updated At [updated_at] (YYYY-MM-DD HH:MM:SS or empty)',
    ],
];
