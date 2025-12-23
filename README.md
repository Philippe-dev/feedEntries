# feedEntries

![Release 2.8](https://img.shields.io/badge/Release-2.8-b7d7ee)
![License AGPL 3.0](https://img.shields.io/badge/License-AGPL_3.0-a5cc52)
![Dotclear 2.36](https://img.shields.io/badge/Dotclear-2.36-137bbb)

Ce petit plugin, sans interface d'administration et ne nécessitant aucune configuration particulière, fournit un jeu complet de balises pour intégrer un flux RSS ou Atom au sein d'un template.

## Les balises

* &lt;tpl:Feed source="url"&gt;&lt;/tpl:Feed&gt;
* {{tpl:FeedTitle}}
* {{tpl:FeedURL}}
* {{tpl:FeedDescription}}
* &lt;tpl:FeedEntries lastn="nb"&gt;&lt;/tpl:FeedEntries&gt;
* &lt;tpl:FeedEntriesHeader&gt;&lt;/tpl:FeedEntriesHeader&gt;
* &lt;tpl:FeedEntriesFooter&gt;&lt;/tpl:FeedEntriesFooter&gt;
* &lt;tpl:FeedEntryIf&gt;&lt;/tpl:FeedEntryIf&gt;
* {{tpl:FeedEntryIfFirst}}
* {{tpl:FeedEntryIfOdd}}
* {{tpl:FeedEntryTitle}}
* {{tpl:FeedEntryURL}}
* {{tpl:FeedEntryAuthor}}
* {{tpl:FeedEntrySummary}}
* {{tpl:FeedEntryExcerpt}}
* {{tpl:FeedEntryContent}}
* {{tpl:FeedEntryPubdate}}
