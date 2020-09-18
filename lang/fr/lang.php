<?php

declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'Redirect',
        'description' => 'Gestion facile des redirections',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Redirects',
            'tab' => 'Redirects',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Redirects',
        'menu_description' => 'Gestion des redirections',
    ],
    'settings' => [
        'menu_label' => 'Redirects',
        'menu_description' => 'Réglages pour les redirections',
        'logging_enabled_label' => 'Log des événements',
        'logging_enabled_comment' => 'Enregistrer les événements dans la BD',
        'statistics_enabled_label' => 'Enregistrer des statistiques',
        'statistics_enabled_comment' => 'Enregistrer des statistiques des redirections pour faire des analyses',
        'test_lab_enabled_label' => 'TestLab (beta)',
        'test_lab_enabled_comment' => 'TestLab vous permet de tester en masse vos redirections.',
        'caching_enabled_label' => 'Caching of redirects (advanced)', // TODO
        'caching_enabled_comment' => 'Improves the redirect engine when having a lot of redirects. ' // TODO
            . 'CAUTION: Cache driver `file` and `database` are NOT supported. ' // TODO
            . 'Recommended driver is `memcached` or a similar "in-memory" caching driver.', // TODO
        'relative_paths_enabled_label' => 'Use relative paths', // TODO
        'relative_paths_enabled_command' => 'The redirect engine will generate relative paths instead of absolute paths.', // TODO
    ],
    'redirect' => [
        'redirect' => 'Redirect',
        'from_url' => 'Chemin source',
        'from_url_placeholder' => '/source/path',
        'from_url_comment' => 'Le chemin source à trouver',
        'from_scheme' => 'Protocole source',
        'from_scheme_comment' => 'Force la correspondance. Si HTTP est selectionné. <u>http://domain.com/path</u> va être selectionner mais'
            . 'pas <u>https://domain.com/path</u>',
        'to_url' => 'Chemin cible ou URL',
        'to_url_placeholder' => '/chemin/cible or http://cible.url',
        'to_url_comment' => 'Le chemin cible ou l\'url vers laquelle rediriger.',
        'to_url_required_if' => 'Le chemin cible ou l\'url est requis',
        'to_scheme' => 'Protocole de destinations',
        'to_scheme_comment' => 'Le protocole de destination est forcé à HTTP ou HTTPS '
            . 'ou choisissez AUTOMATIQUE pour utiliser le protocole par défault du site.',
        'scheme_auto' => 'Automatique',
        'cms_page_required_if' => 'Veuillez spécifier une CMS Page de destination',
        'static_page_required_if' => 'Veuillez spécifier une Static Page de destination',
        'match_type' => 'Type de correspondance',
        'exact' => 'Exacte',
        'regex' => 'Regular expression', // TODO
        'placeholders' => 'Placeholders',
        'target_type' => 'Type de cible',
        'target_type_none' => 'Pas applicable',
        'target_type_path_or_url' => 'Chemin ou URL',
        'target_type_cms_page' => 'CMS Page',
        'target_type_static_page' => 'Static Page',
        'status_code' => 'Code HTTP',
        'sort_order' => 'Ordre de tri',
        'requirements' => 'Conditions',
        'requirements_comment' => 'Spécifier une condition pour chaque placeholder.',
        'placeholder' => 'Placeholder',
        'placeholder_comment' => 'Le nom du placeholder (en includant les accolades) '
            . 'renseigné dans le champ \'Chemin source\'. Ex. {category} ou {id}',
        'requirement' => 'Condition',
        'requirement_comment' => 'Défini la condition avec la syntaxe des expressions régulières. '
            . 'Ex. [0-9]+ ou [a-zA-Z]+.',
        'requirements_prompt' => 'Ajouter une nouvelle condition',
        'replacement' => 'Remplacement',
        'replacement_comment' => 'Fournit une valeur de remplacement optionnel pour ce placeholder. '
            . 'Le placeholder correspondant sera remplacé par cette valeur dans l\'URL cible',
        'permanent' => '301 - Permanente',
        'temporary' => '302 - Temporaire',
        'see_other' => '303 - Document remplacé',
        'not_found' => '404 - Page non trouvée',
        'gone' => '410 - Ressource plus disponible',
        'enabled' => 'Activée',
        'none' => 'Aucune',
        'enabled_comment' => 'Actionnez ce switch pour activer la redirection.',
        'priority' => 'Priorité',
        'hits' => 'Hits',
        'return_to_redirects' => 'Retour à la liste des redirections',
        'return_to_categories' => 'Retour à la liste des catégories',
        'delete_confirm' => 'Êtes-vous sûr?',
        'created_at' => 'Créé à',
        'modified_at' => 'Modifié à',
        'system_tip' => 'Redirection générée par le système',
        'user_tip' => 'Redirection générée par l\'utilisateur',
        'type' => 'Type',
        'last_used_at' => 'Dernière utilisation à',
        'category' => 'Catégorie',
        'categories' => 'Catégories',
        'description' => 'Description', // TODO
        'name' => 'Nom',
        'date_time' => 'Date & Heure',
        'date' => 'Date',
        'truncate_confirm' => 'Voulez-vous effacer tous les enregistrements?',
        'truncating' => 'Effacement...',
        'warning' => 'Warning', // TODO
        'cache_warning' => 'You have enabled caching but your caching driver is not supported. ' // TODO
            . 'Redirects will not be cached.',
        'general_confirm' => 'Are you sure you want to do this?', // TODO
        'sparkline_30d' => 'Hits (30d)', // TODO
        'has_hits' => 'Has hits', // TODO
        'minimum_hits' => 'Minimum # hits', // TODO
        'ignore_query_parameters' => 'Ignore query parameters', // TODO
        'ignore_query_parameters_comment' => 'The redirect engine will ignore all query parameters.', // TODO
        'ignore_case' => 'Ignore case', // TODO
        'ignore_case_comment' => 'The redirect engine will do case-insensitive matching.', // TODO
        'ignore_trailing_slash' => 'Ignore trailing slash', // TODO
        'ignore_trailing_slash_comment' => 'The redirect engine will ignore trailing slashes.', // TODO
        'last_used_at' => 'Last hit', // TODO
        'updated_at' => 'Updated at', // TODO
        'invalid_regex' => 'Invalid regular expression.', // TODO
    ],
    'list' => [
        'no_records' => 'Il n\'y a pas de redirections dans cette vue.',
        'switch_is_enabled' => 'Activée',
        'switch_system' => 'Redirections du système',
    ],
    'scheduling' => [
        'from_date' => 'Date de début',
        'from_date_comment' => 'La date à laquelle cette redirection sera disponible. Optionnel',
        'to_date' => 'Date de fin',
        'to_date_comment' => 'La date d\'expiration de cette redirection. Optionnel',
        'scheduling_comment' => 'Ici vous pouvez spécifier la période durant laquelle la redirection sera disponible. '
            . 'Toutes sortes de combinaisons de dates sont possibles.',
        'not_active_warning' => 'Redirect is not available anymore, please check \'Scheduling\' tab.', // TODO
    ],
    'test' => [
        'test_comment' => 'S\'il vous plaît, testez votre redirection avant de l\'enregistrer.',
        'input_path' => 'Chemin d\'entrée',
        'input_path_comment' => 'Le chemin d\'entrée à tester. Ex. /old-blog/category/123',
        'input_path_placeholder' => '/chemin/a/tester',
        'input_scheme' => 'Input scheme', // TODO
        'test_date' => 'Date du test',
        'test_date_comment' => 'Si vous avez planifié cette redirections, '
            . 'vous pouvez la tester à une date spécifique.',
        'testing' => 'Test en cours...',
        'run_test' => 'Lancer le test',
        'no_match_label' => 'Désolé, aucune correspondance',
        'no_match' => 'Pas de correspondance trouvée',
        'match_success_label' => 'Correspondance trouvée',
    ],
    'test_lab' => [
        'section_test_lab_comment' => 'TestLab vous permet de tester en masse vos redirections.',
        'test_lab_label' => 'Inclure dans TestLab',
        'test_lab_enable' => 'Permettre à TestLab de tester cette redirection.',
        'test_lab_path_label' => 'Tester le chemin',
        'test_lab_path_comment' => 'Ce chemin va être utilisé pour les tests. '
            . 'Replacer les placeholders avec les valeurs réels .',
        'start_tests' => 'Démarrer les tests',
        'start_tests_description' => 'Appuyer sur  \'Démarrer les tests\' pour commencer.',
        'edit' => 'Editer',
        'exclude' => 'Exclure',
        'exclude_confirm' => 'Voulez vous vraiment exclure cette redirection de TestLab?',
        'exclude_indicator' => 'Exclure la redirection de TestLab',
        're_run' => 'Retester',
        're_run_indicator' => 'Tests en progression...',
        'loop' => 'Boucle',
        'match' => 'Correspond',
        'response_http_code' => 'Code de HTTP',
        'response_http_code_should_be' => 'La code de response HTTP devrait être un de ces status:',
        'redirect_count' => 'Nombre de redirections',
        'final_destination' => 'Destination finale',
        'no_redirects' => 'Aucun redirection activée pour TestLab. '
            . 'Activer l\'option \'Inclure dans TestLab\' lors de l\'édition d\'une redirection',
        'test_error' => 'Une erreure est survenue durant le test de cette redirection',
        'flash_test_executed' => 'Le test a été excecuté',
        'flash_redirect_excluded' => 'La redirection a été exclue de TestLab et ne va pas être prise en considération lors du prochain test.',
        'result_request_failed' => 'La requête n\'a aboutis .',
        'redirects_followed' => 'Nombre de redirections suivie: :count (limité à :limit)',
        'not_determinate_destination_url' => 'Impossible de déterminé l\'URL de destination',
        'no_destination_url' => 'Pas d\'URL de destination.',
        'final_destination_is' => 'La cible est: :destination',
        'possible_loop' => 'Boucle de redirection détectée',
        'no_loop' => 'Pas de boucle redirection.',
        'not_match_redirect' => 'Ne correspond pas à une redirection.',
        'matched' => 'Correspond',
        'redirect' => 'redirigé',
        'matched_not_http_code' => 'La redirection correspond, mais pas le code HTTP! '
            . 'Attendu :expected. Reçu :received.',
        'matched_http_code' => 'Redirection trouvée, code de response HTTP :code.',
        'executing_tests' => 'Executing tests...', // TODO
    ],
    'statistics' => [
        'hits_per_day' => 'Redirections par jour',
        'click_on_chart' => 'Cliquer sur le graphique pour zoomer et déplacer.',
        'requests_redirected' => 'Total',
        'all_time' => 'Toute la periode de temps',
        'active_redirects' => 'Redirections actives',
        'redirects_this_month' => 'Redirections du mois',
        'previous_month' => 'Mois précédent',
        'latest_redirected_requests' => 'Dernière redirections',
        'redirects_per_month' => 'Redirections par mois',
        'no_data' => 'Pas de donnée',
        'top_crawlers_this_month' => 'Top :top: crawlers du mois',
        'top_redirects_this_month' => 'Top :top: redirections du mois',
        'activity_last_three_months' => 'Activity last 3 months', // TODO
    ],
    'title' => [
        'import' => 'Import',
        'export' => 'Export',
        'redirects' => 'Gestion des redirections',
        'create_redirect' => 'Création d\'une redirection',
        'edit_redirect' => 'Edition de redirection',
        'categories' => 'Gérer les categories',
        'create_category' => 'Créer une categorie',
        'edit_category' => 'Editer une categorie',
        'view_redirect_log' => 'Voir les logs de redirections',
        'statistics' => 'Statistiques',
        'test_lab' => 'TestLab (beta)',
    ],
    'buttons' => [
        'add' => 'Ajouter',
        'from_request_log' => 'A partir des logs',
        'new_redirect' => 'Ajouter',
        'create_redirects' => 'Créer redirection',
        'create_redirect' => 'Create redirect', // TODO
        'create_and_new' => 'Create and new', // TODO
        'delete' => 'Supprimer',
        'enable' => 'Activer',
        'disable' => 'Désactiver',
        'reorder_redirects' => 'Réordonner',
        'export' => 'Exporter',
        'import' => 'Importer',
        'settings' => 'Settings', // TODO
        'categories' => 'Catégories',
        'extensions' => 'Extensions', // TODO
        'new_category' => 'Nouvelle catégorie',
        'reset_statistics' => 'Réinitialisation des statistiques',
        'logs' => 'Log de redirection',
        'empty_redirect_log' => 'Vider le log de redirection',
        'clear_cache' => 'Clear cache', // TODO
        'stop' => 'Stop', // TODO
        'reset_all' => 'Reset statistics for all redirects', // TODO
        'all_redirects' => 'all redirects', // TODO
        'bulk_actions' => 'Bulk actions', // TODO
    ],
    'tab' => [
        'tab_general' => 'Général',
        'tab_requirements' => 'Conditions',
        'tab_test' => 'Test',
        'tab_scheduling' => 'Planification',
        'tab_test_lab' => 'TestLab',
        'tab_advanced' => 'Avancé',
        'tab_logs' => 'Event log', // TODO
    ],
    'flash' => [
        'success_created_redirects' => ':count redirections crée avec succés',
        'static_page_redirect_not_supported' => 'Cette redirection ne peut pas être modifiée. Plugin RainLab.Pages est nécessaire.',
        'truncate_success' => 'Toute les redirections ont été supprimées',
        'delete_selected_success' => 'Les redirections selectionnée ont été supprimées',
        'cache_cleared_success' => 'Successfully cleared redirect cache', // TODO
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
