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
    'Pep and contributors',
    '3.0',
    [
        'date'        => '2026-05-01T00:00:08+0100',
        'requires' => [
            ['core', '2.36'],
            ['TemplateHelper'],
        ],
        'permissions' => 'My',
        'type'    => 'plugin',
        'support' => 'https://github.com/Philippe-dev/feedEntries',
    ]
);
