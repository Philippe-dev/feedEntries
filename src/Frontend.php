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

namespace Dotclear\Plugin\feedEntries;

use dcCore;
use dcNsProcess;
use html;
use path;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_RC_PATH');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        
        dcCore::app()->tpl->addBlock(self::class, 'Feed']);
        dcCore::app()->tpl->addValue(self::class, 'FeedTitle']);
        dcCore::app()->tpl->addValue(self::class, 'FeedURL']);
        dcCore::app()->tpl->addValue(self::class, 'FeedDescription']);
        dcCore::app()->tpl->addBlock(self::class, 'FeedEntries']);
        dcCore::app()->tpl->addBlock(self::class, 'FeedEntriesHeader']);
        dcCore::app()->tpl->addBlock(self::class, 'FeedEntriesFooter']);
        dcCore::app()->tpl->addBlock(self::class, 'FeedEntryIf']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntryIfFirst']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntryIfOdd']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntryTitle']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntryURL']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntryAuthor']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntrySummary']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntryExcerpt']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntryContent']);
        dcCore::app()->tpl->addValue(self::class, 'FeedEntryPubdate']);

        return true;
    }

    public static function publicHeadContent()
    {
        // Settings

        $s = dcCore::app()->blog->settings->colorbox;

        if (!$s->colorbox_enabled) {
            return;
        }

        $url = dcCore::app()->blog->getQmarkURL() . 'pf=colorbox';

        echo
        '<link rel="stylesheet" type="text/css" href="' . $url . '/css/colorbox_common.css" />' . "\n" .
        '<link rel="stylesheet" type="text/css" href="' . $url . '/themes/' . $s->colorbox_theme . '/colorbox_theme.css" />' . "\n";

        if ($s->colorbox_user_files) {
            $public_path        = dcCore::app()->blog->public_path;
            $public_url         = dcCore::app()->blog->settings->system->public_url;
            $colorbox_user_path = $public_path . '/colorbox/themes/';
            $colorbox_user_url  = $public_url . '/colorbox/themes/';

            if (file_exists($colorbox_user_path . $s->colorbox_theme . '/colorbox_user.css')) {
                echo
                '<link rel="stylesheet" type="text/css" href="' . $colorbox_user_url . $s->colorbox_theme . '/colorbox_user.css" />' . "\n";
            }
        } else {
            $theme_path         = path::fullFromRoot(dcCore::app()->blog->settings->system->themes_path . '/' . dcCore::app()->blog->settings->system->theme, DC_ROOT);
            $theme_url          = dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme;
            $colorbox_user_path = $theme_path . '/colorbox/themes/' . $s->colorbox_theme . '/colorbox_user.css';
            $colorbox_user_url  = $theme_url . '/colorbox/themes/' . $s->colorbox_theme . '/colorbox_user.css';
            if (file_exists($colorbox_user_path)) {
                echo
                '<link rel="stylesheet" type="text/css" href="' . $colorbox_user_url . '" />' . "\n";
            }
        }
    }

    public static function publicFooterContent($core)
    {
        // Settings

        $s = dcCore::app()->blog->settings->colorbox;

        if (!$s->colorbox_enabled) {
            return;
        }

        $url = dcCore::app()->blog->getQmarkURL() . 'pf=colorbox';

        $icon_name   = 'zoom.png';
        $icon_width  = '16';
        $icon_height = '16';

        echo
        '<script src="' . $url . '/js/jquery.colorbox-min.js"></script>' . "\n" .
        '<script>' . "\n" .
        "//<![CDATA[\n";

        $selectors = '.post' . ($s->colorbox_selectors !== '' ? ',' . $s->colorbox_selectors : '');

        echo
        '$(function () {' . "\n" .
            'var count = 0; ' .
            '$("' . $selectors . '").each(function() {' . "\n" .
                'count++;' . "\n" .
                '$(this).find(\'a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],' .
                'a[href$=".JPG"],a[href$=".JPEG"],a[href$=".PNG"],a[href$=".GIF"]\').addClass("colorbox_zoom");' . "\n" .
                '$(this).find(\'a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],' .
                'a[href$=".JPG"],a[href$=".JPEG"],a[href$=".PNG"],a[href$=".GIF"]\').attr("rel", "colorbox-"+count);' . "\n";

        if ($s->colorbox_zoom_icon_permanent) {
            echo
            '$(this).find("a.colorbox_zoom").each(function(){' . "\n" .
                'var p = $(this).find("img");' . "\n" .
                'if (p.length != 0){' . "\n" .
                    'var offset = p.offset();' . "\n" .
                    'var parent = p.offsetParent();' . "\n" .
                    'var offsetparent = parent.offset();' . "\n" .
                    'var parenttop = offsetparent.top;' . "\n" .
                    'var parentleft = offsetparent.left;' . "\n" .
                    'var top = offset.top-parenttop;' . "\n";

            if ($s->colorbox_position) {
                echo 'var left = offset.left-parentleft;' . "\n";
            } else {
                echo 'var left = offset.left-parentleft+p.outerWidth()-' . $icon_width . ';' . "\n";
            }

            echo '$(this).append("<span style=\"z-index:10;width:' . $icon_width . 'px;height:' . $icon_height . 'px;top:' . '"+top+"' . 'px;left:' . '"+left+"' . 'px;background: url(' . html::escapeJS($url) . '/themes/' . $s->colorbox_theme . '/images/zoom.png) top left no-repeat; position:absolute;\"></span>");' . "\n" .
                '}' . "\n" .
            '});' . "\n";
        }

        if ($s->colorbox_zoom_icon && !$s->colorbox_zoom_icon_permanent) {
            echo
            '$(\'body\').prepend(\'<img id="colorbox_magnify" style="display:block;padding:0;margin:0;z-index:10;width:' . $icon_width . 'px;height:' . $icon_height . 'px;position:absolute;top:0;left:0;display:none;" src="' . html::escapeJS($url) . '/themes/' . $s->colorbox_theme . '/images/zoom.png" alt=""  />\');' . "\n" .
            '$(\'img#colorbox_magnify\').on(\'click\', function ()' . "\n" .
                '{ ' . "\n" .
                    '$("a.colorbox_zoom img.colorbox_hovered").click(); ' . "\n" .
                    '$("a.colorbox_zoom img.colorbox_hovered").removeClass(\'colorbox_hovered\');' . "\n" .
                '});' . "\n" .
                '$(\'a.colorbox_zoom img\').on(\'click\', function ()' . "\n" .
                '{ ' . "\n" .
                    '$(this).removeClass(\'colorbox_hovered\');' . "\n" .
                '});' . "\n" .
                '$("a.colorbox_zoom img").hover(function(){' . "\n" .

                'var p = $(this);' . "\n" .
                'p.addClass(\'colorbox_hovered\');' . "\n" .
                'var offset = p.offset();' . "\n";

            if (!$s->colorbox_position) {
                echo '$(\'img#colorbox_magnify\').css({\'top\' : offset.top, \'left\' : offset.left+p.outerWidth()-' . $icon_width . '});' . "\n";
            } else {
                echo '$(\'img#colorbox_magnify\').css({\'top\' : offset.top, \'left\' : offset.left});' . "\n";
            }
            echo
            '$(\'img#colorbox_magnify\').show();' . "\n" .
            '},function(){' . "\n" .
                'var p = $(this);' . "\n" .
                'p.removeClass(\'colorbox_hovered\');' . "\n" .
                '$(\'img#colorbox_magnify\').hide();' . "\n" .
            '});' . "\n";
        }

        foreach (unserialize($s->colorbox_advanced) as $k => $v) {
            if ($v === '') {
                if ($k == 'title' && $s->colorbox_legend == 'alt') {
                    $opts[] = $k . ': function(){return $(this).find(\'img\').attr(\'alt\');}';
                } elseif ($k == 'title' && $s->colorbox_legend == 'title') {
                    $opts[] = $k . ': function(){return $(this).attr(\'title\');}';
                } elseif ($k == 'title' && $s->colorbox_legend == 'none') {
                    $opts[] = $k . ': \'\'';
                } else {
                    $opts[] = $k . ': false';
                }
            } elseif (is_bool($v)) {
                $opts[] = $k . ': ' . ($v ? 'true' : 'false');
            } elseif (is_numeric($v)) {
                $opts[] = $k . ': ' . $v;
            } elseif (is_string($v)) {
                if ($k == 'onOpen' || $k == 'onLoad' || $k == 'onComplete' || $k == 'onCleanup' || $k == 'onClosed') {
                    $opts[] = $k . ': function(){return ' . $v . '}';
                } else {
                    $opts[] = $k . ": '" . $v . "'";
                }
            }
        }

        echo
        "});\n" .
        '$("a[rel*=\'colorbox-\']").colorbox({' . implode(",\n", $opts) . '});' . "\n" .
        "});\n" .
        "\n//]]>\n" .
        "</script>\n";
    }
}
