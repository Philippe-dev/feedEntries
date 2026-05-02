# feedEntries

![Release 3.0](https://img.shields.io/badge/Release-3.0-b7d7ee)
![License AGPL 3.0](https://img.shields.io/badge/License-AGPL_3.0-a5cc52)
![Dotclear 2.36](https://img.shields.io/badge/Dotclear-2.36-137bbb)

Ce plugin fournit un jeu de balises pour intégrer un flux RSS ou Atom au sein d'un template.

## Pré-requis

Nécessite le plugin Template Helper.

## Les balises

* &lt;tpl:Feed source=”url”&gt;&lt;/tpl:Feed&gt;
* &lt;tpl:FeedEntries lastn=”nb”&gt;&lt;/tpl:FeedEntries&gt;
* &lt;tpl:FeedEntriesHeader&gt;&lt;/tpl:FeedEntriesHeader&gt;
* &lt;tpl:FeedEntriesFooter&gt;&lt;/tpl:FeedEntriesFooter&gt;
* {{tpl:FeedEntryTitle}}
* {{tpl:FeedEntryURL}}
* {{tpl:FeedEntryAuthor}}
* {{tpl:FeedEntryContent}}
* {{tpl:FeedEntryPubdate}}

## Exemple

```language-xml
<h3><a href="https://dotclear.org/actualites">Actualités</a></h3>
<tpl:Feed source="https://dotclear.org/feed/category/Actualités/atom">
  <ul>
    <tpl:FeedEntries lastn="5">
      <li><a href="{{tpl:FeedEntryURL}}">{{tpl:FeedEntryTitle encode_html="1"}}</a>
        <ul>
          <li>publié le {{tpl:FeedEntryPubdate}}</li>
          <li>par {{tpl:FeedEntryAuthor}}</li>
        </ul>
      </li>
    </tpl:FeedEntries>
  </ul>
</tpl:Feed>
```
