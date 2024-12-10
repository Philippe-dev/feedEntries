<?php
/**
 * @brief feedEntries, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Pep
 *
 * @copyright GPL-2.0 [https://www.gnu.org/licenses/gpl-2.0.html]
 */
$this->registerModule(
    'feedEntries',
    'Integrate feed entries in your templates',
    'Pep',
    '2.4',
    [
        'requires'    => [['core', '2.26']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
        'type'    => 'plugin',
        'support' => 'https://github.com/Philippe-dev/feedEntries',
    ]
);
