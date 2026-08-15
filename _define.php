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
declare(strict_types=1);

if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'feedEntries',
        'Integrate feed entries in your templates',
        'Pep and contributors',
        '3.1',
        [
            'date'     => '2026-08-15T00:00:08+0100',
            'requires' => [
                ['core', '2.39'],
                ['TemplateHelper'],
            ],
            'permissions' => 'My',
            'type'        => 'plugin',
            'support'     => 'https://github.com/Philippe-dev/feedEntries',
        ]
    );
}
