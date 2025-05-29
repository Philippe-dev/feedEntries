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
    '2.5',
    [
        'requires'    => [['core', '2.26']],
        'permissions' => 'My',
        'type'    => 'plugin',
        'support' => 'https://github.com/Philippe-dev/feedEntries',
    ]
);
