<?php

declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'Redirect',
        'description' => 'Administra facilmente las redirecciones',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Redirecciones',
            'tab' => 'Redirecciones',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Redirecciones',
        'menu_description' => 'Administra las redirecciones',
    ],
    'settings' => [
        'menu_label' => 'Redirección',
        'menu_description' => 'Administrar la configuración de las redirecciones.',
        'logging_enabled_label' => 'Eventos de redireccionamiento',
        'logging_enabled_comment' => 'Almacena los eventos de redireccionamiento en la base de datos.',
        'statistics_enabled_label' => 'Recopilar estadísticas',
        'statistics_enabled_comment' => 'Reúne las estadísticas de las solicitudes redirigidas para obtener más información.',
        'test_lab_enabled_label' => 'Laboratorio de pruebas(beta)',
        'test_lab_enabled_comment' => 'El laboratorio de pruebas le permite probar en masa sus redirecciones.',
        'caching_enabled_label' => 'Caching de redirecciones (avanzado)',
        'caching_enabled_comment' => 'Mejora el motor de redireccionamiento cuando se tienen muchas redirecciones. '
            . 'CAUTION: Cache driver `file` and `database` are NOT supported. ' // TODO
            . 'Recommended driver is `memcached` or a similar "in-memory" caching driver.', // TODO
        'relative_paths_enabled_label' => 'Use relative paths', // TODO
        'relative_paths_enabled_command' => 'The redirect engine will generate relative paths instead of absolute paths.', // TODO
    ],
    'redirect' => [
        'redirect' => 'Redirección',
        'from_url' => 'Ruta de origen',
        'from_url_placeholder' => '/tu/ruta',
        'from_url_comment' => 'Ruta de origen a coincidir.',
        'from_scheme' => 'Esquema fuente',
        'from_scheme_comment' => 'Force match on scheme. If HTTP is selected <u>http://domain.com/path</u> will '
            . 'match and <u>https://domain.com/path</u> does not match.', // TODO
        'to_url' => 'Ruta destino o URL',
        'to_url_placeholder' => '/ruta/absoluta, ruta/relativa o http://destino.url', // changed since 2.0.6
        'to_url_comment' => 'Ruta destino o URL a la cual deseas redirigir.',
        'to_url_required_if' => 'El destino o la URL son requeridos',
        'to_scheme' => 'Target scheme', // TODO
        'to_scheme_comment' => 'Target scheme will be forced to HTTP or HTTPS '
            . 'or choose AUTOMATIC to use the default scheme of the website.', // TODO
        'scheme_auto' => 'Automático',
        'input_path_placeholder' => '/input/path',
        'cms_page_required_if' => 'Por favor ingresa una página del CMS a la cual redirigir',
        'static_page_required_if' => 'Por favor ingresa una página estática a la cual deseas redirigir',
        'match_type' => 'Coincidir Tipo',
        'exact' => 'Exacto',
        'regex' => 'Expresión regular',
        'placeholders' => 'Marcadores',
        'target_type' => 'Tipo de destino',
        'target_type_none' => 'No se aplica.',
        'target_type_path_or_url' => 'Ruta o URL',
        'target_type_cms_page' => 'Página CMS',
        'target_type_static_page' => 'Página estática',
        'status_code' => 'Código de estado HTTP',
        'sort_order' => 'Orden de clasificación',
        'requirements' => 'Requerimientos',
        'requirements_comment' => 'Proporciona los requerimientos para cada marcador.',
        'placeholder' => 'Marcador',
        'placeholder_comment' => 'El marcador (incluyendo las llaves) proporcionadas en el campo \'ruta de origen\'. '
            . 'Por ejemplo {category} o {id}',
        'requirement' => 'Requerimiento',
        'requirement_comment' => 'Proporciona el requerimiento con una expresión regular. Ej. [0-9]+ o [a-zA-Z]+.',
        'requirements_prompt' => 'Agregar nuevo requerimiento',
        'replacement' => 'Reemplazo',
        'replacement_comment' => 'Proporciona un valor de reemplazo para este marcador. '
            . 'El marcador coincidente será reemplazado con el valor de la URL destino.',
        'permanent' => '301 - Permanente',
        'temporary' => '302 - Temporal',
        'see_other' => '303 - Ver otro',
        'not_found' => '404 - No encontrado',
        'gone' => '410 - Ya no existe',
        'enabled' => 'Activado',
        'none' => 'ninguno',
        'enabled_comment' => 'Activa este switch para activar esta redirección.',
        'priority' => 'Prioridad',
        'hits' => 'Visitas',
        'return_to_redirects' => 'Regresar al listado de redirecciones',
        'return_to_categories' => 'Regresar al listado de categorías',
        'delete_confirm' => '¿Estás seguro?',
        'created_at' => 'Creado el',
        'modified_at' => 'Modificado el',
        'system_tip' => 'Redirección generada por el sistema',
        'user_tip' => 'Redirección generada por el usuario',
        'type' => 'Tipo',
        'last_used_at' => 'Usada última vez',
        'and_delete_log_item' => 'y borra los elementos seleccionados', // since 2.0.3,
        'category' => 'Categoría',
        'categories' => 'Categorías',
        'description' => 'Descripción',
        'name' => 'Nombre',
        'date_time' => 'Fecha y Hora',
        'date' => 'Fecha',
        'truncate_confirm' => '¿Estás seguro que deseas borrar todos los registros?',
        'truncating' => 'Borrando...',
        'warning' => 'Advertencia',
        'cache_warning' => 'You have enabled caching but your caching driver is not supported. ' // TODO
            . 'Redirects will not be cached.',
        'general_confirm' => '¿Estás seguro de que quieres hacer esto?',
        'sparkline_30d' => 'visitas (30d)',
        'has_hits' => 'Tiene visitas',
        'minimum_hits' => 'Mínimo # visitas',
        'ignore_query_parameters' => 'Ignore query parameters', // TODO
        'ignore_query_parameters_comment' => 'The redirect engine will ignore all query parameters.', // TODO
        'ignore_case' => 'Ignore case', // TODO
        'ignore_case_comment' => 'The redirect engine will do case-insensitive matching.', // TODO
        'ignore_trailing_slash' => 'Ignore trailing slash', // TODO
        'ignore_trailing_slash_comment' => 'The redirect engine will ignore trailing slashes.', // TODO
        'last_used_at' => 'Last hit',  // TODO
        'updated_at' => 'Updated at',  // TODO
        'invalid_regex' => 'Expresión regular inválida.',
    ],
    'list' => [
        'no_records' => 'No hay redirecciones en esta vista.',
        'switch_is_enabled' => 'Activado',
        'switch_system' => 'Redirecciones del sistema',
    ],
    'scheduling' => [
        'from_date' => 'Disponible desde',
        'from_date_comment' => 'La fecha en que esta redirección se activará puede ser omitida.',
        'to_date' => 'Disponible hasta',
        'to_date_comment' => 'La fecha limite de esta redirección, puede ser omitida.',
        'scheduling_comment' => 'Aquí puedes establecer el periodo que durara la redirección. '
            . 'Todo tipo de combinaciones de fechas es posible.',
        'not_active_warning' => 'La redirección ya no está disponible, por favor, compruebe la pestaña \'Scheduling\' .',
    ],
    'test' => [
        'test_comment' => 'Por favor prueba tu redirección antes de guardar la ruta.',
        'input_path' => 'Ruta de entrada',
        'input_path_comment' => 'Ruta de entrada a probar Ej. /old-blog/category/123',
        'input_path_placeholder' => '/ruta/de/entrada',
        'input_scheme' => 'Esquema de entrada',
        'test_date' => 'Fecha de prueba',
        'test_date_comment' => 'Si tu calendarizas esta redirección, tu puedes probar esta redirección en una fecha concreta.',
        'testing' => 'Probando...',
        'run_test' => 'Ejecutar prueba',
        'no_match_label' => 'Lo siento, no hay coincidencia!',
        'no_match' => '¡No se encontraron coincidencias!',
        'match_success_label' => 'Se encontró una coincidencia!',
    ],
    'test_lab' => [
        'section_test_lab_comment' => 'El laboratorio de pruebas le permite probar en masa sus redirecciones.',
        'test_lab_label' => 'Incluir en el laboratorio de pruebas',
        'test_lab_enable' => 'Pulse este interruptor para permitir probar esta redirección en el laboratorio de pruebas.',
        'test_lab_path_label' => 'Ruta de prueba',
        'test_lab_path_comment' => 'Esta ruta se usará cuando se realicen pruebas. '
            . 'Reemplazar los marcadores de posición por valores reales.',
        'start_tests' => 'Iniciar las pruebas',
        'start_tests_description' => 'Presione el botón \'Start tests\' para comenzar.',
        'edit' => 'Editar',
        'exclude' => 'Excluir',
        'exclude_confirm' => '¿Está seguro de que quiere excluir esta redirección del laboratorio de pruebas?',
        'exclude_indicator' => 'Excluyendo la redirección desde el laboratorio de pruebas',
        're_run' => 'Reejecutar',
        're_run_indicator' => 'Haciendo pruebas, por favor espere...',
        'loop' => 'Bucle',
        'match' => 'Coincidencia',
        'response_http_code' => 'Respuesta Código HTTP',
        'response_http_code_should_be' => 'El código HTTP de respuesta debería ser uno de:',
        'redirect_count' => 'Redirigir el conteo',
        'final_destination' => 'Destino final',
        'no_redirects' => 'No se han marcado redirecciones con el laboratorio de pruebas habilitado. '
            . 'Por favor, habilite la opción \'Include in TestLab\' cuando edite una redirección.',
        'test_error' => 'Se produjo un error al probar esta redirección.',
        'flash_test_executed' => 'La prueba ha sido ejecutada.',
        'flash_redirect_excluded' => 'La redirección ha sido excluida del laboratorio de pruebas y no aparecerá en la próxima prueba.',
        'result_request_failed' => 'No pudo ejecutar la solicitud.',
        'redirects_followed' => 'Número de redirecciones seguidas :count (limited to :limit)',
        'not_determinate_destination_url' => 'No se pudo determinar el URL de destino final.',
        'no_destination_url' => 'No hay un URL de destino final.',
        'final_destination_is' => 'El destino final es: :destination',
        'possible_loop' => '¡Posible bucle de redireccionamiento!',
        'no_loop' => 'No se detecta ningún bucle de redireccionamiento.',
        'not_match_redirect' => 'No coincide con ninguna redirección.',
        'matched' => 'Coincide',
        'redirect' => 'redirigir',
        'matched_not_http_code' => '¡Una redirección coincidente, pero el código HTTP de respuesta no coincide! '
            . 'Esperado :esperado pero recibido :recibido.',
        'matched_http_code' => 'Redirección coincidente, respuesta código HTTP: código.',
        'executing_tests' => 'Ejecutando pruebas...',
    ],
    'statistics' => [
        'hits_per_day' => 'Redireccionar las visitas por día',
        'click_on_chart' => 'Haga clic en el gráfico para activar el zoom y el desplazamiento.',
        'requests_redirected' => 'Solicitudes redirigidas',
        'all_time' => 'todo el tiempo',
        'active_redirects' => 'Activa las redirecciones',
        'redirects_this_month' => 'Las redirecciones de este mes',
        'previous_month' => 'el mes anterior',
        'latest_redirected_requests' => 'Última solicitud redirigida',
        'redirects_per_month' => 'Redirecciones por mes',
        'no_data' => 'No hay datos',
        'top_crawlers_this_month' => 'Arriba: los principales rastreadores de este mes',
        'top_redirects_this_month' => 'principales :Principales redirecciones este mes',
        'activity_last_three_months' => 'Activity last 3 months', // TODO
    ],
    'title' => [
        'import' => 'Importar',
        'export' => 'Exportar',
        'redirects' => 'Administrar las redirecciones',
        'create_redirect' => 'Crear una redirección',
        'edit_redirect' => 'Editar una redirección',
        'categories' => 'Administrar las categorías',
        'create_category' => 'Crear una categoría',
        'edit_category' => 'Modificar una categoría',
        'view_redirect_log' => 'Ver log de redirecciones',
        'statistics' => 'Estadísticas',
        'test_lab' => 'Laboratorio de pruebas (beta)',
    ],
    'buttons' => [
        'add' => 'Agregar', // since 2.0.3
        'from_request_log' => 'Del log de peticiones', // since 2.0.3
        'new_redirect' => 'Nueva redirección', // changed since 2.0.3
        'create_redirects' => 'Crear redirecciones', // since 2.0.3
        'create_redirect' => 'Crear redirección',
        'create_and_new' => 'Crear y nuevo',
        'delete' => 'Borrar',
        'enable' => 'Activar',
        'disable' => 'Desactivar',
        'reorder_redirects' => 'Reordenar',
        'export' => 'Exportar',
        'import' => 'Importar',
        'settings' => 'Settings', // TODO
        'categories' => 'Categorías',
        'extensions' => 'Extensions', // TODO
        'new_category' => 'Nueva categoría',
        'reset_statistics' => 'Limpiar estadísticas',
        'logs' => 'Log de redirecciones',
        'empty_redirect_log' => 'Vaciar log de redirecciones',
        'clear_cache' => 'Limpiar la memoria caché',
        'stop' => 'Detener',
        'reset_all' => 'Reset statistics for all redirects', // TODO
        'all_redirects' => 'all redirects', // TODO
        'bulk_actions' => 'Bulk actions', // TODO
    ],
    'tab' => [
        'tab_general' => 'General', // TODO
        'tab_requirements' => 'Requerimientos',
        'tab_test' => 'Probar',
        'tab_scheduling' => 'Calendarizar',
        'tab_test_lab' => 'Laboratorio de pruebas',
        'tab_advanced' => 'Avanzado',
        'tab_logs' => 'Event log', // TODO
    ],
    'flash' => [
        'success_created_redirects' => 'Se crearon con éxito :count redirecciones', // since 2.0.3
        'static_page_redirect_not_supported' => 'Esta redirección no puede ser modificada. El Plugin RainLab.Pages es requerido.',
        'truncate_success' => 'Todos los registros han sido borrados con éxito',
        'delete_selected_success' => 'Los registros seleccionados fueron borrados con éxito',
        'cache_cleared_success' => 'Se ha limpiado con éxito el caché de redireccionamiento',
        'statistics_reset_success' => 'All statistics have been successfully reset', // TODO
        'enabled_all_redirects_success' => 'All redirects have been successfully enabled', // TODO
        'disabled_all_redirects_success' => 'All redirects have been successfully disabled', // TODO
        'deleted_all_redirects_success' => 'All redirects have been successfully deleted', // TODO
    ],
    'import_export' => [
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
        'ignore_query_parameters' => 'Ignore Query Parameters [ignore_query_parameters] (1 = yes, 0 = no [default])',
        'ignore_case' => 'Ignore Case [ignore_case] (1 = yes, 0 = no [default])',
        'ignore_trailing_slash' => 'Ignore Trailing Slashes [ignore_trailing_slash] (1 = yes, 0 = no [default])',
        'test_lab' => 'Test Lab [test_lab] (1 = enable Test Lab, 0 = disable TestLab [default])',
        'test_lab_path' => 'Test Lab path [test_lab_path] (required if match_type = placeholders)',
        'system' => 'System [system] (1 = system generated redirect, 0 = user generated redirect [default])',
        'description' => 'Description [description]',
        'last_used_at' => 'Last Used At [last_used_at] (YYYY-MM-DD HH:MM:SS or empty)',
        'created_at' => 'Created At [created_at] (YYYY-MM-DD HH:MM:SS or empty)',
        'updated_at' => 'Updated At [updated_at] (YYYY-MM-DD HH:MM:SS or empty)',
    ],
];
