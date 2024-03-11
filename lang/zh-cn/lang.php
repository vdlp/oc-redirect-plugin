<?php

declare(strict_types=1);

return [
    'plugin' => [
        'name' => '重定向',
        'description' => '轻松管理重定向',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => '重定向',
            'tab' => '重定向',
        ],
    ],
    'navigation' => [
        'menu_label' => '重定向',
        'menu_description' => '管理重定向',
    ],
    'settings' => [
        'menu_label' => '重定向',
        'menu_description' => '管理重定向的设置。',
        'logging_enabled_label' => '记录重定向事件',
        'logging_enabled_comment' => '将重定向事件存储在数据库中。',
        'statistics_enabled_label' => '收集统计信息',
        'statistics_enabled_comment' => '收集重定向请求的统计信息以获得更多见解。',
        'test_lab_enabled_label' => 'TestLab(测试版)',
        'test_lab_enabled_comment' => 'TestLab 允许您批量测试您的重定向。',
        'caching_enabled_label' => '重定向缓存(高级)',
        'caching_enabled_comment' => '当有很多重定向时，可以改进重定向引擎。 '
            . '注意：不支持缓存驱动程序“file”和“database”。 '
            . '推荐的驱动程序是“memcached”或类似的“in-memory”缓存驱动程序。',
        'relative_paths_enabled_label' => '使用相对路径',
        'relative_paths_enabled_command' => '重定向引擎将生成相对路径，而不是绝对路径。',
    ],
    'redirect' => [
        'redirect' => '重新使用',
        'from_url' => '源路径',
        'from_url_placeholder' => '/source/path',
        'from_url_comment' => '匹配的源路径。',
        'from_scheme' => '源方案',
        'from_scheme_comment' => '方案强制匹配，如果选择了HTTP，则http://domain.com/path将匹配'
            . 'https://domain.com/path不匹配',
        'to_url' => '目标路径或URL',
        'to_url_placeholder' => '/绝对路径，相对路径或http://目标网址',
        'to_url_comment' => '重定向的目标路径或URL。',
        'to_url_required_if' => '需要目标路径或URL',
        'to_scheme' => '目标方案',
        'to_scheme_comment' => '目标方案将强制使用HTTP或HTTPS'
            . '或者选择AUTOMATIC以使用网站的默认方案。',
        'scheme_auto' => '自动的',
        'cms_page_required_if' => '请提供一个CMS页面进行跳转',
        'static_page_required_if' => '请提供一个静态页面进行重定向',
        'match_type' => '匹配类型',
        'exact' => '准确的',
        'placeholders' => '占位符',
        'regex' => '正则表达式',
        'target_type' => '目标类型',
        'target_type_none' => '不适用',
        'target_type_path_or_url' => '路径或URL',
        'target_type_cms_page' => 'CMS页面',
        'target_type_static_page' => '静态页面',
        'status_code' => 'HTTP 状态代码',
        'sort_order' => '排序顺序',
        'requirements' => '需求说明',
        'requirements_comment' => '为每个占位符提供要求。',
        'placeholder' => '占位符',
        'placeholder_comment' => '“源路径”字段中提供的占位符名称(包括花括号)'
            . '例如：{category} 或 {id}',
        'requirement' => '要求',
        'requirement_comment' => '提供正则表达式语法中的要求。例如[0-9]+或[a-zA-Z]+。',
        'requirements_prompt' => '添加新要求',
        'replacement' => '替换',
        'replacement_comment' => '为此占位符提供可选替换值。 '
            . '匹配的占位符将在目标URL中替换为此值。',
        'permanent' => '301 - 永久重定向',
        'temporary' => '302 - 临时重定向',
        'see_other' => '303 - 请参阅其他内容',
        'not_found' => '404 - 未找到',
        'gone' => '410 - 已消失',
        'enabled' => '已启用',
        'none' => '没有一个',
        'enabled_comment' => '选中此框以启用重定向。',
        'priority' => '优先事项',
        'hits' => '# 点击次数',
        'return_to_redirects' => '返回重定向列表',
        'return_to_categories' => '返回类别列表',
        'delete_confirm' => '你确定吗',
        'created_at' => '创建于',
        'modified_at' => '修改日期：',
        'system_tip' => '系统生成的重定向',
        'user_tip' => '用户生成的跳转',
        'type' => '类型',
        'category' => '类别',
        'categories' => '分类列表',
        'description' => '描述',
        'name' => '名称',
        'date_time' => '日期和时间',
        'date' => '日期',
        'truncate_confirm' => '您确定要删除所有记录吗？',
        'truncating' => '正在删除...',
        'warning' => '警告',
        'cache_warning' => '您已启用缓存，但您的缓存驱动程序不受支持。'
            . '重定向不会被缓存。',
        'general_confirm' => '你确定要这样做吗？',
        'sparkline_30d' => '点击数(30天)',
        'has_hits' => '有点击',
        'minimum_hits' => '最低点击次数',
        'ignore_query_parameters' => '忽略查询参数',
        'ignore_query_parameters_comment' => '重定向引擎将忽略所有查询参数。',
        'ignore_case' => '忽略大小写',
        'ignore_case_comment' => '重定向引擎将进行不区分大小写的匹配。',
        'ignore_trailing_slash' => '忽略尾部斜线',
        'ignore_trailing_slash_comment' => '重定向引擎将忽略尾随的斜杠。',
        'keep_querystring' => '继承查询字符串',
        'keep_querystring_comment' => '任何存在的查询参数都会传递到目标路径或URL。',
        'last_used_at' => '最后一次击中',
        'updated_at' => '更新于',
        'invalid_regex' => '正则表达式无效。',
        'created_due_to_bad_request' => '由于请求错误而创建。',
    ],
    'list' => [
        'no_records' => '此视图中没有重定向。',
        'switch_is_enabled' => '已启用',
        'switch_system' => '系统重定向',
    ],
    'scheduling' => [
        'from_date' => '可用时间',
        'from_date_comment' => '此重定向将生效的日期。可以省略。',
        'to_date' => '可用至',
        'to_date_comment' => '此重定向生效前的日期。可以省略。',
        'scheduling_comment' => '您可以在这里提供一个时间段，'
            . '此重定向将在此时间段内可用。可以使用各种日期组合。',
        'not_active_warning' => '重定向已不可用，请检查“计划”选项卡。',
    ],
    'test' => [
        'test_comment' => '请在保存重定向之前测试您的重定向。',
        'input_path' => '输入路径',
        'input_path_comment' => '要测试的输入路径。例如：/old-blog/category/123',
        'input_path_placeholder' => '/input/path',
        'input_scheme' => '输入方案',
        'test_date' => '测试日期',
        'test_date_comment' => '如果您计划了此重定向，则可以在特定日期测试此重定向。',
        'testing' => '测试中...',
        'run_test' => '运行测试',
        'no_match_label' => '抱歉，没有匹配项！',
        'no_match' => '未找到匹配项！',
        'match_success_label' => '我们找到了匹配项！',
    ],
    'test_lab' => [
        'section_test_lab_comment' => 'TestLab允许您批量测试您的重定向。',
        'test_lab_label' => '包含在TestLab中',
        'test_lab_enable' => '拨动此开关以允许在TestLab中测试此重定向。',
        'test_lab_path_label' => '测试路径',
        'test_lab_path_comment' => '在进行测试时，将使用这个路径。'
            . '请将占位符替换为实际的值。',
        'start_tests' => '开始测试',
        'start_tests_description' => '请点击“开始测试”按钮以开始。',
        'edit' => '编辑',
        'exclude' => '排除',
        'exclude_confirm' => '您确定要从TestLab中排除此重定向吗？',
        'exclude_indicator' => '从TestLab中排除重定向',
        're_run' => '重新运行',
        're_run_indicator' => '正在运行测试，请稍候...',
        'loop' => '循环',
        'match' => '匹配',
        'response_http_code' => '响应HTTP代码',
        'response_http_code_should_be' => '响应HTTP代码应为以下之一：',
        'redirect_count' => '重定向次数',
        'final_destination' => '最终目的地',
        'no_redirects' => '没有启用TestLab的重定向。'
            . '请在编辑重定向时启用“包含在TestLab中”的选项。',
        'test_error' => '测试此重定向时发生错误。',
        'flash_test_executed' => '测试已执行。',
        'flash_redirect_excluded' => '已从TestLab中排除此重定向，下次运行时将不会显示。',
        'result_request_failed' => '无法执行请求。',
        'redirects_followed' => '跟踪的重定向次数：:count(限制为:limit)',
        'not_determinate_destination_url' => '无法确定最终的目标URL。',
        'no_destination_url' => '没有最终的目标URL。',
        'final_destination_is' => '最终的目标URL是：:destination',
        'possible_loop' => '可能存在重定向循环！',
        'no_loop' => '未检测到重定向循环。',
        'not_match_redirect' => '未匹配任何重定向。',
        'matched' => '已匹配',
        'redirect' => '重定向',
        'matched_not_http_code' => '已匹配重定向，但响应的HTTP代码不匹配！ '
            . '期望的是:expected，但收到的是:received。',
        'matched_http_code' => '匹配重定向，响应 HTTP 代码 :code。',
        'executing_tests' => '正在执行测试...',
    ],
    'statistics' => [
        'hits_per_day' => '每天重定向点击数',
        'click_on_chart' => '点击图表可启用缩放和拖动功能。',
        'requests_redirected' => '请求已重定向',
        'all_time' => '所有时间',
        'active_redirects' => '主动重定向',
        'redirects_this_month' => '本月重定向',
        'previous_month' => '上个月份',
        'latest_redirected_requests' => '最新重定向请求',
        'redirects_per_month' => '每月重定向次数',
        'no_data' => '尚无数据',
        'top_crawlers_this_month' => '顶部：本月热门爬虫',
        'top_redirects_this_month' => 'Top :本月热门重定向',
        'activity_last_three_months' => '活动持续 3 个月',
        'crawler_hits' => '爬虫命中',
        'visitor_hits' => '访客点击数',
    ],
    'title' => [
        'import' => '导入',
        'export' => '导出',
        'redirects' => '管理重定向',
        'create_redirect' => '创建重定向',
        'edit_redirect' => '编辑重定向',
        'categories' => '管理类别',
        'create_category' => '创建类别',
        'edit_category' => '编辑类别',
        'view_redirect_log' => '事件日志',
        'statistics' => '统计',
        'test_lab' => '实验室(测试版)',
    ],
    'buttons' => [
        'add' => '添加',
        'from_request_log' => '来自请求日志',
        'new_redirect' => '新建重定向',
        'create_redirects' => '创建重定向',
        'create_redirect' => '创建重定向',
        'create_and_new' => '创建和新建',
        'delete' => '删除',
        'enable' => '启用',
        'disable' => '禁用',
        'reorder_redirects' => '重新排序',
        'export' => '导出',
        'import' => '导入',
        'settings' => '设置',
        'categories' => '分类目录',
        'extensions' => '扩展',
        'new_category' => '新类别',
        'reset_statistics' => '重置统计信息',
        'logs' => '事件日志',
        'empty_redirect_log' => '清空事件日志',
        'clear_cache' => '清除缓存',
        'stop' => '停止',
        'reset_all' => '重置所有重定向的统计信息',
        'all_redirects' => '所有重定向',
        'bulk_actions' => '批量操作',
    ],
    'tab' => [
        'tab_general' => '常规',
        'tab_requirements' => '要求：',
        'tab_test' => '测验',
        'tab_scheduling' => '日程安排',
        'tab_test_lab' => '测试实验室',
        'tab_advanced' => '高级',
        'tab_logs' => '事件日志',
    ],
    'flash' => [
        'success_created_redirects' => '已成功创建 :count 个重定向',
        'static_page_redirect_not_supported' => '无法修改此重定向。需要插件 RainLab.Pages。',
        'truncate_success' => '已成功删除所有记录',
        'delete_selected_success' => '已成功删除所选记录',
        'cache_cleared_success' => '已成功清除重定向缓存',
        'statistics_reset_success' => '所有统计数据已成功重置',
        'enabled_all_redirects_success' => '已成功启用所有重定向',
        'disabled_all_redirects_success' => '已成功禁用所有重定向',
        'deleted_all_redirects_success' => '已成功删除所有重定向',
    ],
    'import_export' => [
        'match_type' => '匹配类型 [match_type](允许值：exact、占位符、正则表达式)',
        'category_id' => '类别 [category_id]',
        'target_type' => '目标类型 [target_type](允许值：path_or_url、cms_page、static_page、none)',
        'from_url' => '源路径 [from_url]',
        'from_scheme' => '源方案 [from_scheme](允许值：http、https、auto [默认值])',
        'to_url' => '目标路径 [to_url]',
        'to_scheme' => '目标方案 [to_scheme](允许值：http、https、auto [默认值])',
        'test_url' => '测试 URL [test_url]',
        'cms_page' => 'CMS页面[cms_page](文件名无.htm扩展名)',
        'static_page' => '静态页面 [static_page](不带 .htm 扩展名的文件名)',
        'requirements' => '占位符要求[要求]',
        'status_code' => 'HTTP 状态代码 [status_code](可能值：301、302、303、404、410)',
        'hits' => '重定向点击数 [hits]',
        'from_date' => '计划日期从 [from_date] (YYYY-MM-DD 或空)',
        'to_date' => '计划日期至[to_date](YYYY-MM-DD或空白)',
        'sort_order' => '优先级 [sort_order]',
        'is_enabled' => '启用 [is_enabled](1 = 启用重定向，0 = 禁用重定向[默认值])',
        'ignore_query_parameters' => '忽略查询参数 [ignore_query_parameters](1=是，0=否[默认值])',
        'ignore_case' => '忽略大小写 [ignore_case](1=是，0=否[默认值])',
        'ignore_trailing_slash' => '忽略尾部斜线 [ignore_trailing_slash](1=是，0=否[默认值])',
        'test_lab' => '测试实验室 [test_lab](1 = 启用测试实验室，0 = 禁用测试实验室 [默认值])',
        'test_lab_path' => '测试实验室路径 [test_lab_path](如果 match_type = placeholders，则必须填写)',
        'system' => '系统 [system](1 = 系统生成的重定向，0 = 用户生成的重定向[默认值])',
        'description' => '描述 [描述]',
        'last_used_at' => '上次使用时间 [last_used_at] (YYYY-MM-DD HH:MM:SS 或为空)',
        'created_at' => '创建时间 [created_at] (YYYY-MM-DD HH:MM:SS 或为空)',
        'updated_at' => '更新时间 [updated_at] (YYYY-MM-DD HH:MM:SS 或为空)',
    ],
    'html_lang' => [
        'status_code_comment' =>'<label>HTTP状态码 <span class="status-code-info icon-question-circle"data-control="popup" data-handler="onShowStatusCodeInfo" data-keyboard="true" data-size="huge"></span></label>',
        'status_code_info' => '<h3>301 (永久重定向)</h3>
        <p>301 (Moved Permanently) 状态代码表示目标资源已经被分配了一个新的永久性URI，任何未来对此资源的引用都应该使用所包含的URIs之一。具有链接编辑功能的客户端应该自动将有效请求URI的引用重新链接到服务器发送的一个或多个新引用，如果可能的话。</p>
    
        <h3>302 (临时重定向)</h3>
        <p>302 (Found) 状态代码表示目标资源暂时位于另一个不同的URI下。由于重定向可能会随时改变，客户端应该继续使用有效的请求URI进行未来的请求。
        </p>
            
        <h3>303 (参阅其他内容)</h3>
        <p>303（See Other）状态代码表示服务器正在将用户代理重定向到另一个资源，如Location头字段中的URI所指示，其目的是为原始请求提供一个间接响应。用户代理可以针对该URI执行检索请求（如果使用HTTP，则为GET或HEAD请求），这也可能被重定向，并将最终结果作为原始请求的答案呈现。请注意，Location头字段中的新URI并不等同于有效的请求URI。</p>
            
        <h3>404 (未找到)</h3>
        <p>404（未找到）状态代码表示源服务器找不到目标资源的当前表示，或者不愿意披露存在该资源。404状态代码并不表明这种缺乏表示是暂时的还是永久的；如果源服务器知道（可能是通过某种可配置的方式），这种情况很可能是永久的，那么410（已消失）状态代码比404更可取。</</p>
            
        <h3>410 (已消失)</h3>
        <p>410 (Gone) 状态代码表示在源服务器上对目标资源的访问已不再可用，并且这种情况很可能是永久的。如果源服务器不知道，或者没有设施来确定这种情况是否是永久的，那么应该使用404 (Not Found) 状态代码来替代。</p>',

    ],
];
