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

use ArrayObject;
use Dotclear\Helper\Network\Http;
use Dotclear\Plugin\TemplateHelper\Code;

class FrontendTemplate
{
    /**
     * Start a feed block
     *
     * <tpl:Feed source="url"></tpl:Feed>
     *
     * Attribute :source = URL of the feed to fetch and render (required)
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function Feed(array|ArrayObject $attr, string $content): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        if (empty($attr['source'])) {
            return '';
        }

        if (!empty($attr['source']) && strpos($attr['source'], '/') === 0) {
            $attr['source'] = Http::getHost() . $attr['source'];
        }

        return Code::getPHPTemplateBlockCode(
            FrontendTemplateCode::Feed(...),
            [],
            $content,
            $attr,
        );
    }

    /**
     * Start the loop to process each entry in the current feed
     *
     * <tpl:FeedEntries lastn="nb"></tpl:FeedEntries>
     *
     * Attribute :lastn = Number of entries to show (optional, default to 10)
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function FeedEntries(array|ArrayObject $attr, string $content): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        $lastn = 10;
        if (isset($attr['lastn'])) {
            $lastn = abs((int) $attr['lastn']) + 0;
        }

        return Code::getPHPTemplateBlockCode(
            FrontendTemplateCode::FeedEntries(...),
            [],
            $content,
            $attr,
        );
    }

    /**
     * Display a block at the start of the entries loop
     *
     * <tpl:FeedEntriesHeader></tpl:FeedEntriesHeader>
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function FeedEntriesHeader(array|ArrayObject $attr, string $content): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateBlockCode(
            FrontendTemplateCode::FeedEntriesHeader(...),
            [],
            $content,
            $attr,
        );
    }

    /**
     * Display a block at the end of the entries loop
     *
     * <tpl:FeedEntriesFooter></tpl:FeedEntriesFooter>
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function FeedEntriesFooter(array|ArrayObject $attr, string $content): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateBlockCode(
            FrontendTemplateCode::FeedEntriesFooter(...),
            [],
            $content,
            $attr,
        );
    }

    /**
     * Display the title of the current entry
     *
     * {{tpl:FeedEntryTitle}}
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function FeedEntryTitle(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::FeedEntryTitle(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }

    /**
     * Display the source URL of the current entry
     *
     * {{tpl:FeedEntryTitle}}
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function FeedEntryURL(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::FeedEntryURL(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }

    /**
     * Display the author of the current entry
     *
     * {{tpl:FeedEntryAuthor}}
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function FeedEntryAuthor(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::FeedEntryAuthor(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }

    /**
     * Display the publication date and/or time of the current entry
     *
     * {{tpl:FeedEntryPubdate format="strftime"}}
     *
     * Attribute :format = Format string compatible with PHP strftime()
     * (optional, default to the date_format setting of the running blog)
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function FeedEntryPubdate(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::FeedEntryPubdate(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }

    /**
     * Display the full content of the current entry
     *
     * {{tpl:FeedEntryContent}}
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function FeedEntryContent(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::FeedEntryContent(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }
}
