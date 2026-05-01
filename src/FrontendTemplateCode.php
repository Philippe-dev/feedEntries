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

class FrontendTemplateCode
{
    /**
     * PHP code for tpl:Feed block
     */
    public static function Feed(
        string $_content_HTML,
        array $_source,
    ): void {
        App::frontend()->context()->feed = \Dotclear\Helper\Network\Feed\Reader::quickParse($_source['source'], App::config()->cacheRoot());

        if (App::frontend()->context()->feed) : ?>
            $_content_HTML
            <?php endif;
    }

    /**
     * PHP code for tpl:FeedEntries block
     */
    public static function FeedEntries(
        string $_content_HTML,
        array $_lastn,
    ): void {
        if (App::frontend()->context()->feed && count(App::frontend()->context()->feed->items))  :
            $nb_feed_items = min(count(App::frontend()->context()->feed->items), $_lastn['lastn']);
            for (App::frontend()->context()->feed_idx = 0; App::frontend()->context()->feed_idx < $nb_feed_items; App::frontend()->context()->feed_idx++) : ?>
            $_content_HTML
            <?php endfor;
            unset(App::frontend()->context()->feed_idx,$nb_feed_items);
        endif;
    }

    /**
     * PHP code for tpl:FeedEntriesHeader block
     */
    public static function FeedEntriesHeader(
        string $_content_HTML,
    ): void {
        if (App::frontend()->context()->feed_idx == 0) : ?>
        $_content_HTML
        <?php endif;
    }

    /**
     * PHP code for tpl:FeedEntriesFooter block
     */
    public static function FeedEntriesFooter(
        string $_content_HTML,
    ): void {
        if (App::frontend()->context()->feed_idx == ($nb_feed_items - 1)) : ?>
        $_content_HTML
        <?php endif;
    }

    /**
     * PHP code for tpl:FeedEntryTitle value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function FeedEntryTitle(
        string $_id_HTML,
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->title,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:FeedEntryURL value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function FeedEntryURL(
        string $_id_HTML,
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->link,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:FeedEntryAuthor value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function FeedEntryAuthor(
        string $_id_HTML,
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->creator,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:FeedEntryPubdate value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function FeedEntryPubdate(
        string $_id_HTML,
        array $_params_,
        string $_tag_
    ): void {
        $fmt = App::blog()->settings->system->date_format;
        if (!empty($attr['format'])) {
            $fmt = $attr['format'];
        }

        echo App::frontend()->context()::global_filters(
            \Dotclear\Helper\Date::str($fmt, App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->TS, App::blog()->settings->system->blog_timezone),
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:FeedEntryContent value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function FeedEntryContent(
        string $_id_HTML,
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->feed->items[App::frontend()->context()->feed_idx]->content,
            $_params_,
            $_tag_
        );
    }
}
