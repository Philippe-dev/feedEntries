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
use Dotclear\Helper\Process\TraitProcess;

class Frontend
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        App::frontend()->template()->addBlock('Feed', FrontendTemplate::Feed(...));
        App::frontend()->template()->addBlock('FeedEntries', FrontendTemplate::FeedEntries(...));
        App::frontend()->template()->addBlock('FeedEntriesHeader', FrontendTemplate::FeedEntriesHeader(...));
        App::frontend()->template()->addBlock('FeedEntriesFooter', FrontendTemplate::FeedEntriesFooter(...));
        App::frontend()->template()->addValue('FeedEntryTitle', FrontendTemplate::FeedEntryTitle(...));
        App::frontend()->template()->addValue('FeedEntryURL', FrontendTemplate::FeedEntryURL(...));
        App::frontend()->template()->addValue('FeedEntryAuthor', FrontendTemplate::FeedEntryAuthor(...));
        App::frontend()->template()->addValue('FeedEntryPubdate', FrontendTemplate::FeedEntryPubdate(...));
        App::frontend()->template()->addValue('FeedEntryContent', FrontendTemplate::FeedEntryContent(...));

        return true;
    }
}
