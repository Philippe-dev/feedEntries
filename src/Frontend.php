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

        dcCore::app()->tpl->addBlock('Feed', [self::class, 'Feed']);
        dcCore::app()->tpl->addValue('FeedTitle', [self::class, 'FeedTitle']);
        dcCore::app()->tpl->addValue('FeedURL', [self::class, 'FeedURL']);
        dcCore::app()->tpl->addValue('FeedDescription', [self::class, 'FeedDescription']);
        dcCore::app()->tpl->addBlock('FeedEntries', [self::class, 'FeedEntries']);
        dcCore::app()->tpl->addBlock('FeedEntriesHeader', [self::class, 'FeedEntriesHeader']);
        dcCore::app()->tpl->addBlock('FeedEntriesFooter', [self::class, 'FeedEntriesFooter']);
        dcCore::app()->tpl->addBlock('FeedEntryIf', [self::class, 'FeedEntryIf']);
        dcCore::app()->tpl->addValue('FeedEntryIfFirst', [self::class, 'FeedEntryIfFirst']);
        dcCore::app()->tpl->addValue('FeedEntryIfOdd', [self::class, 'FeedEntryIfOdd']);
        dcCore::app()->tpl->addValue('FeedEntryTitle', [self::class, 'FeedEntryTitle']);
        dcCore::app()->tpl->addValue('FeedEntryURL', [self::class, 'FeedEntryURL']);
        dcCore::app()->tpl->addValue('FeedEntryAuthor', [self::class, 'FeedEntryAuthor']);
        dcCore::app()->tpl->addValue('FeedEntrySummary', [self::class, 'FeedEntrySummary']);
        dcCore::app()->tpl->addValue('FeedEntryExcerpt', [self::class, 'FeedEntryExcerpt']);
        dcCore::app()->tpl->addValue('FeedEntryContent', [self::class, 'FeedEntryContent']);
        dcCore::app()->tpl->addValue('FeedEntryPubdate', [self::class, 'FeedEntryPubdate']);

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
            'dcCore::app()->ctx->feed = ' . Reader::class . '::quickParse("' . $attr['source'] . '",DC_TPL_CACHE); ' . "\n" .
            'if (dcCore::app()->ctx->feed !== null) : ?>' . "\n" .
            $content . "\n" .
            '<?php unset(dcCore::app()->ctx->feed); ' . "\n" .
            'endif; ?>' . "\n";
    }

    /**
     * Display the title of the current feed
     * {{tpl:FeedTitle}}
     */
    public static function FeedTitle($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->feed->title') . '; ?>';
    }

    /**
     * Display the source URL of the current feed
     * {{tpl:FeedURL}}
     */
    public static function FeedURL($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->feed->link') . '; ?>';
    }

    /**
     * Display the description of the current feed
     * {{tpl:FeedDescription}}
     */
    public static function FeedDescription($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->feed->description') . '; ?>';
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
            'if (count(dcCore::app()->ctx->feed->items)) : ' . "\n" .
            '$nb_feed_items = min(count(dcCore::app()->ctx->feed->items),' . $lastn . ');' . "\n" .
            'for (dcCore::app()->ctx->feed_idx = 0; dcCore::app()->ctx->feed_idx < $nb_feed_items; dcCore::app()->ctx->feed_idx++) : ?>' . "\n" .
            $content . "\n" .
            '<?php endfor;' . "\n" .
            'unset(dcCore::app()->ctx->feed_idx,$nb_feed_items); ' . "\n" .
            'endif; ?>' . "\n";
    }

    /**
     * Display a block at the start of the entries loop
     * <tpl:FeedEntriesHeader></tpl:FeedEntriesHeader>
     */
    public static function FeedEntriesHeader($attr, $content)
    {
        return
        "<?php if (\dcCore::app()->ctx->feed_idx == 0) : ?>" .
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
        "<?php if (\dcCore::app()->ctx->feed_idx == ($nb_feed_items - 1)) : ?>" .
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

        $operator = isset($attr['operator']) ? dcCore::app()->tpl->getOperator($attr['operator']) : '&&' ;

        if (isset($attr['first'])) {
            $sign = (bool) $attr['first'] ? '=' : '!';
            $if[] = 'dcCore::app()->ctx->feed_idx ' . $sign . '= 0';
        }

        if (isset($attr['odd'])) {
            $sign = (bool) $attr['odd'] ? '=' : '!';
            $if[] = '(dcCore::app()->ctx->feed_idx+1)%2 ' . $sign . '= 1';
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
        '<?php if (dcCore::app()->ctx->feed_idx == 0) { ' .
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
        '<?php if ((dcCore::app()->ctx->feed_idx+1)%2 == 1) { ' .
        "echo '" . addslashes($ret) . "'; } ?>";
    }

    /**
     * Display the title of the current entry
     * {{tpl:FeedEntryTitle}}
     */
    public static function FeedEntryTitle($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->title') . '; ?>';
    }

    /**
     * Display the source URL of the current entry
     * {{tpl:FeedEntryURL}}
     */
    public static function FeedEntryURL($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->link') . '; ?>';
    }

    /**
     * Display the author of the current entry
     * {{tpl:FeedEntryAuthor}}
     */
    public static function FeedEntryAuthor($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->creator') . '; ?>';
    }

    /**
     * Display the summary of the current entry.
     * {{tpl:FeedEntrySummary}}
     */
    public static function FeedEntrySummary($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->description') . '; ?>';
    }

    /**
     * Display an excerpt of the current entry.
     * {{tpl:FeedEntryExcerpt}}
     */
    public static function FeedEntryExcerpt($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, self::class . '::getExcerpt()') . '; ?>';
    }

    /**
     * Display the full content of the current entry
     * {{tpl:FeedEntryContent}}
     */
    public static function FeedEntryContent($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->content') . '; ?>';
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
        $fmt = dcCore::app()->blog->settings->system->date_format;
        if (!empty($attr['format'])) {
            $fmt = $attr['format'];
        }
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'Dotclear\Helper\Date::str("' . $fmt . '",dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->TS,dcCore::app()->blog->settings->system->blog_timezone)') . '; ?>';
    }

    public static function getExcerpt()
    {
        if (!dcCore::app()->ctx->feed || is_null(dcCore::app()->ctx->feed_idx)) {
            return;
        }

        if (dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->description) {
            return dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->description;
        }

        return Html::clean(dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->content);
    }

    public static function isExtended()
    {
        if (!dcCore::app()->ctx->feed || is_null(dcCore::app()->ctx->feed_idx)) {
            return false;
        }

        return (dcCore::app()->ctx->feed->items[dcCore::app()->ctx->feed_idx]->description != '');
    }
}
