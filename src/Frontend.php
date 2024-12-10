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

use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Network\Http;
use Dotclear\Helper\Network\Feed\Reader;

class Frontend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        App::frontend()->template()->addBlock('Feed', [self::class, 'Feed']);
        App::frontend()->template()->addValue('FeedTitle', [self::class, 'FeedTitle']);
        App::frontend()->template()->addValue('FeedURL', [self::class, 'FeedURL']);
        App::frontend()->template()->addValue('FeedDescription', [self::class, 'FeedDescription']);
        App::frontend()->template()->addBlock('FeedEntries', [self::class, 'FeedEntries']);
        App::frontend()->template()->addBlock('FeedEntriesHeader', [self::class, 'FeedEntriesHeader']);
        App::frontend()->template()->addBlock('FeedEntriesFooter', [self::class, 'FeedEntriesFooter']);
        App::frontend()->template()->addBlock('FeedEntryIf', [self::class, 'FeedEntryIf']);
        App::frontend()->template()->addValue('FeedEntryIfFirst', [self::class, 'FeedEntryIfFirst']);
        App::frontend()->template()->addValue('FeedEntryIfOdd', [self::class, 'FeedEntryIfOdd']);
        App::frontend()->template()->addValue('FeedEntryTitle', [self::class, 'FeedEntryTitle']);
        App::frontend()->template()->addValue('FeedEntryURL', [self::class, 'FeedEntryURL']);
        App::frontend()->template()->addValue('FeedEntryAuthor', [self::class, 'FeedEntryAuthor']);
        App::frontend()->template()->addValue('FeedEntrySummary', [self::class, 'FeedEntrySummary']);
        App::frontend()->template()->addValue('FeedEntryExcerpt', [self::class, 'FeedEntryExcerpt']);
        App::frontend()->template()->addValue('FeedEntryContent', [self::class, 'FeedEntryContent']);
        App::frontend()->template()->addValue('FeedEntryPubdate', [self::class, 'FeedEntryPubdate']);

        return true;
    }

    /**
     * Start a feed block
     * <tpl:Feed source="url"></tpl:Feed>
     *
     * Attribute(s) :
     * - source = URL of the feed to fetch and render (required)
     */
    public static function Feed($attr, $content)
    {
        if (empty($attr['source'])) {
            return;
        }

        if (strpos($attr['source'], '/') === 0) {
            $attr['source'] = Http::getHost() . $attr['source'];
        }

        return
            '<?php' . "\n" .
            'App::frontend()->context()->feed = ' . Reader::class . '::quickParse("' . $attr['source'] . '",DC_TPL_CACHE); ' . "\n" .
            'if (App::frontend()->context()->feed !== null) : ?>' . "\n" .
            $content . "\n" .
            '<?php unset(App::frontend()->context()->feed); ' . "\n" .
            'endif; ?>' . "\n";
    }

    /**
     * Display the title of the current feed
     * {{tpl:FeedTitle}}
     */
    public static function FeedTitle($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->feed->title') . '; ?>';
    }

    /**
     * Display the source URL of the current feed
     * {{tpl:FeedURL}}
     */
    public static function FeedURL($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->feed->link') . '; ?>';
    }

    /**
     * Display the description of the current feed
     * {{tpl:FeedDescription}}
     */
    public static function FeedDescription($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->feed->description') . '; ?>';
    }

    /**
     * Start the loop to process each entry in the current feed
     * <tpl:FeedEntries lastn="nb"></tpl:FeedEntries>
     *
     * Attribute(s) :
     * - lastn = Number of entries to show (optional, default to 10)
     */
    public static function FeedEntries($attr, $content)
    {
        $lastn = 10;
        if (isset($attr['lastn'])) {
            $lastn = abs((int) $attr['lastn']) + 0;
        }

        return
            '<?php' . "\n" .
            'if (count(App::frontend()->context()->feed->items)) : ' . "\n" .
            '$nb_feed_items = min(count(App::frontend()->context()->feed->items),' . $lastn . ');' . "\n" .
            'for (App::frontend()->context()->feed_idx = 0; App::frontend()->context()->feed_idx < $nb_feed_items; App::frontend()->context()->feed_idx++) : ?>' . "\n" .
            $content . "\n" .
            '<?php endfor;' . "\n" .
            'unset(App::frontend()->context()->feed_idx,$nb_feed_items); ' . "\n" .
            'endif; ?>' . "\n";
    }

    /**
     * Display a block at the start of the entries loop
     * <tpl:FeedEntriesHeader></tpl:FeedEntriesHeader>
     */
    public static function FeedEntriesHeader($attr, $content)
    {
        return
        "<?php if (\App::frontend()->context()->feed_idx == 0) : ?>" .
        $content .
        '<?php endif; ?>';
    }

    /**
     * Display a block at the end of the entries loop
     * <tpl:FeedEntriesFooter></tpl:FeedEntriesFooter>
     */
    public static function FeedEntriesFooter($attr, $content)
    {
        return
        "<?php if (\App::frontend()->context()->feed_idx == ($nb_feed_items - 1)) : ?>" .
        $content .
        '<?php endif; ?>';
    }

    /**
     * Display a block only if some conditions are matched.
     * <tpl:FeedEntryIf></tpl:FeedEntryIf>
     *
     * Attribute(s) :
     * - operator (optional) = logical operator used to compute multiple conditions
     * - first (optional) 	= test if the current entry is the first in set
     * - odd (optional) 	= test if the current entry has an odd index in set
     * - extended (optional) = test if the current entry has a complete (non-empty) "description" property
     */
    public static function FeedEntryIf($attr, $content)
    {
        $if = [];

        $operator = isset($attr['operator']) ? App::frontend()->template()->getOperator($attr['operator']) : '&&' ;

        if (isset($attr['first'])) {
            $sign = (bool) $attr['first'] ? '=' : '!';
            $if[] = 'App::frontend()->context()->feed_idx ' . $sign . '= 0';
        }

        if (isset($attr['odd'])) {
            $sign = (bool) $attr['odd'] ? '=' : '!';
            $if[] = '(App::frontend()->context()->feed_idx+1)%2 ' . $sign . '= 1';
        }

        if (isset($attr['extended'])) {
            $sign = (bool) $attr['extended'] ? '' : '!';
            $if[] = $sign . self::class . '::isExtended()';
        }

        if (!empty($if)) {
            return '<?php if(' . implode(' ' . $operator . ' ', $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    /**
     * Return a special class if the current entry is the first of the collection
     * {{tpl:FeedEntryIfFirst}}
     */
    public static function FeedEntryIfFirst($attr)
    {
        $ret = $attr['return'] ?? 'first';
        $ret = Html::escapeHTML($ret);

        return
        '<?php if (App::frontend()->context()->feed_idx == 0) { ' .
        "echo '" . addslashes($ret) . "'; } ?>";
    }

    /**
     * Return a special class if the current entry has an odd index in the collection
     * {{tpl:FeedEntryIfOdd}}
     */
    public static function FeedEntryIfOdd($attr)
    {
        $ret = $attr['return'] ?? 'odd';
        $ret = Html::escapeHTML($ret);

        return
        '<?php if ((App::frontend()->context()->feed_idx+1)%2 == 1) { ' .
        "echo '" . addslashes($ret) . "'; } ?>";
    }

    /**
     * Display the title of the current entry
     * {{tpl:FeedEntryTitle}}
     */
    public static function FeedEntryTitle($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->title') . '; ?>';
    }

    /**
     * Display the source URL of the current entry
     * {{tpl:FeedEntryURL}}
     */
    public static function FeedEntryURL($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->link') . '; ?>';
    }

    /**
     * Display the author of the current entry
     * {{tpl:FeedEntryAuthor}}
     */
    public static function FeedEntryAuthor($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->creator') . '; ?>';
    }

    /**
     * Display the summary of the current entry.
     * {{tpl:FeedEntrySummary}}
     */
    public static function FeedEntrySummary($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->description') . '; ?>';
    }

    /**
     * Display an excerpt of the current entry.
     * {{tpl:FeedEntryExcerpt}}
     */
    public static function FeedEntryExcerpt($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, self::class . '::getExcerpt()') . '; ?>';
    }

    /**
     * Display the full content of the current entry
     * {{tpl:FeedEntryContent}}
     */
    public static function FeedEntryContent($attr)
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->content') . '; ?>';
    }

    /**
     * Display the publication date and/or time of the current entry
     * {{tpl:FeedEntryPubdate format="strftime"}}
     *
     * Attribute(s) :
     * - format = Format string compatible with PHP strftime()
     *            (optional, default to the date_format setting of the running blog)
     */
    public static function FeedEntryPubdate($attr)
    {
        $fmt = App::blog()->settings->system->date_format;
        if (!empty($attr['format'])) {
            $fmt = $attr['format'];
        }
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'Dotclear\Helper\Date::str("' . $fmt . '",App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->TS,App::blog()->settings->system->blog_timezone)') . '; ?>';
    }

    public static function getExcerpt()
    {
        if (!App::frontend()->context()->feed || is_null(App::frontend()->context()->feed_idx)) {
            return;
        }

        if (App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->description) {
            return App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->description;
        }

        return Html::clean(App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->content);
    }

    public static function isExtended()
    {
        if (!App::frontend()->context()->feed || is_null(App::frontend()->context()->feed_idx)) {
            return false;
        }

        return (App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->description != '');
    }
}
