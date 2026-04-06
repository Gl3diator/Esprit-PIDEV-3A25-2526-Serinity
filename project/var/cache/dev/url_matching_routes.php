<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/admin/forum' => [[['_route' => 'app_admin_forum', '_controller' => 'App\\Controller\\AdminController::index'], null, null, null, false, false, null]],
        '/admin/forum/categories/new' => [[['_route' => 'app_admin_category_new', '_controller' => 'App\\Controller\\AdminController::newCategory'], null, null, null, false, false, null]],
        '/admin/forum/statistics' => [[['_route' => 'app_admin_statistics', '_controller' => 'App\\Controller\\AdminController::statistics'], null, null, null, false, false, null]],
        '/forum' => [[['_route' => 'app_forum_feed', '_controller' => 'App\\Controller\\ForumController::feed'], null, null, null, false, false, null]],
        '/forum/my' => [[['_route' => 'app_forum_my_threads', '_controller' => 'App\\Controller\\ForumController::myThreads'], null, null, null, false, false, null]],
        '/forum/followed' => [[['_route' => 'app_forum_followed', '_controller' => 'App\\Controller\\ForumController::followed'], null, null, null, false, false, null]],
        '/forum/archived' => [[['_route' => 'app_forum_archived', '_controller' => 'App\\Controller\\ForumController::archived'], null, null, null, false, false, null]],
        '/' => [[['_route' => 'app_home', '_controller' => 'App\\Controller\\HomeController::index'], null, null, null, false, false, null]],
        '/notifications' => [[['_route' => 'app_notifications', '_controller' => 'App\\Controller\\NotificationController::index'], null, null, null, false, false, null]],
        '/notifications/widget' => [[['_route' => 'app_notifications_widget', '_controller' => 'App\\Controller\\NotificationController::widget'], null, null, null, false, false, null]],
        '/notifications/seen-all' => [[['_route' => 'app_notification_seen_all', '_controller' => 'App\\Controller\\NotificationController::seenAll'], null, null, null, false, false, null]],
        '/threads/new' => [[['_route' => 'app_thread_new', '_controller' => 'App\\Controller\\ThreadManageController::new'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|wdt/([^/]++)(*:24)'
                    .'|profiler/(?'
                        .'|font/([^/\\.]++)\\.woff2(*:65)'
                        .'|([^/]++)(?'
                            .'|/(?'
                                .'|search/results(*:101)'
                                .'|router(*:115)'
                                .'|exception(?'
                                    .'|(*:135)'
                                    .'|\\.css(*:148)'
                                .')'
                            .')'
                            .'|(*:158)'
                        .')'
                    .')'
                .')'
                .'|/admin/forum/categories/([^/]++)/(?'
                    .'|edit(*:209)'
                    .'|delete(*:223)'
                .')'
                .'|/forum/(?'
                    .'|thread/(\\d+)(*:254)'
                    .'|reply/([^/]++)/(?'
                        .'|edit(*:284)'
                        .'|delete(*:298)'
                    .')'
                .')'
                .'|/notifications/([^/]++)/(?'
                    .'|seen(*:339)'
                    .'|open(*:351)'
                    .'|delete(*:365)'
                .')'
                .'|/threads/(?'
                    .'|(\\d+)/edit(*:396)'
                    .'|([^/]++)/(?'
                        .'|d(?'
                            .'|elete(*:425)'
                            .'|ownvote(*:440)'
                        .')'
                        .'|status/([^/]++)(*:464)'
                        .'|pin(*:475)'
                        .'|upvote(*:489)'
                        .'|follow(*:503)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        24 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        65 => [[['_route' => '_profiler_font', '_controller' => 'web_profiler.controller.profiler::fontAction'], ['fontName'], null, null, false, false, null]],
        101 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        115 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        135 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        148 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        158 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        209 => [[['_route' => 'app_admin_category_edit', '_controller' => 'App\\Controller\\AdminController::editCategory'], ['id'], null, null, false, false, null]],
        223 => [[['_route' => 'app_admin_category_delete', '_controller' => 'App\\Controller\\AdminController::deleteCategory'], ['id'], ['POST' => 0], null, false, false, null]],
        254 => [[['_route' => 'app_forum_thread_detail', '_controller' => 'App\\Controller\\ForumController::detail'], ['id'], null, null, false, true, null]],
        284 => [[['_route' => 'app_reply_edit', '_controller' => 'App\\Controller\\ForumController::editReply'], ['id'], ['POST' => 0], null, false, false, null]],
        298 => [[['_route' => 'app_reply_delete', '_controller' => 'App\\Controller\\ForumController::deleteReply'], ['id'], ['POST' => 0], null, false, false, null]],
        339 => [[['_route' => 'app_notification_seen', '_controller' => 'App\\Controller\\NotificationController::seen'], ['id'], null, null, false, false, null]],
        351 => [[['_route' => 'app_notification_open', '_controller' => 'App\\Controller\\NotificationController::open'], ['id'], null, null, false, false, null]],
        365 => [[['_route' => 'app_notification_delete', '_controller' => 'App\\Controller\\NotificationController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        396 => [[['_route' => 'app_thread_edit', '_controller' => 'App\\Controller\\ThreadManageController::edit'], ['id'], null, null, false, false, null]],
        425 => [[['_route' => 'app_thread_delete', '_controller' => 'App\\Controller\\ThreadManageController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        440 => [[['_route' => 'app_thread_downvote', '_controller' => 'App\\Controller\\ThreadManageController::downvote'], ['id'], null, null, false, false, null]],
        464 => [[['_route' => 'app_thread_status', '_controller' => 'App\\Controller\\ThreadManageController::status'], ['id', 'status'], null, null, false, true, null]],
        475 => [[['_route' => 'app_thread_pin', '_controller' => 'App\\Controller\\ThreadManageController::pin'], ['id'], null, null, false, false, null]],
        489 => [[['_route' => 'app_thread_upvote', '_controller' => 'App\\Controller\\ThreadManageController::upvote'], ['id'], null, null, false, false, null]],
        503 => [
            [['_route' => 'app_thread_follow', '_controller' => 'App\\Controller\\ThreadManageController::follow'], ['id'], null, null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
