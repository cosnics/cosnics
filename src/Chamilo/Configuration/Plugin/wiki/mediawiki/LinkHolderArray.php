<?php

use Chamilo\Core\Repository\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class MediawikiLinkHolderArray
{

    var $internals = [], $interwikis = [];

    var $size = 0;

    var $parent;

    function __construct($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Reduce memory usage to reduce the impact of circular references
     */
    function __destruct()
    {
        foreach ($this as $name => $value)
        {
            unset($this->$name);
        }
    }

    /**
     * Merge another LinkHolderArray into this one
     */
    function merge($other)
    {
        foreach ($other->internals as $ns => $entries)
        {
            $this->size += count($entries);
            if (! isset($this->internals[$ns]))
            {
                $this->internals[$ns] = $entries;
            }
            else
            {
                $this->internals[$ns] += $entries;
            }
        }
        $this->interwikis += $other->interwikis;
    }

    /**
     * Returns true if the memory requirements of this object are getting large
     */
    function isBig()
    {
        // CHAMILO | The default is 1000, let's just use it.
        // global $wgLinkHolderBatchSize;
        // return $this->size > $wgLinkHolderBatchSize;
        return $this->size > 1000;
    }

    /**
     * Clear all stored link holders.
     * Make sure you don't have any text left using these link holders, before you call
     * this
     */
    function clear()
    {
        $this->internals = [];
        $this->interwikis = [];
        $this->size = 0;
    }

    /**
     * Make a link placeholder.
     * The text returned can be later resolved to a real link with replaceLinkHolders(). This
     * is done for two reasons: firstly to avoid further parsing of interwiki links, and secondly to allow all existence
     * checks and article length checks (for stub links) to be bundled into a single query.
     */
    function makeHolder($nt, $text = '', $query = '', $trail = '', $prefix = '')
    {
        if (! is_object($nt))
        {
            // Fail gracefully
            $retVal = "<!-- ERROR -->{$prefix}{$text}{$trail}";
        }
        else
        {
            // Separate the link trail from the rest of the link
            list($inside, $trail) = MediaWikiLinker::splitTrail($trail);

            $entry = array('title' => $nt, 'text' => $prefix . $text . $inside, 'pdbk' => $nt->getPrefixedDBkey());
            if ($query !== '')
            {
                $entry['query'] = $query;
            }

            if ($nt->isExternal())
            {
                // Use a globally unique ID to keep the objects mergable
                $key = $this->parent->nextLinkID();
                $this->interwikis[$key] = $entry;
                $retVal = "<!--IWLINK $key-->{$trail}";
            }
            else
            {
                $key = $this->parent->nextLinkID();
                $ns = $nt->getNamespace();
                $this->internals[$ns][$key] = $entry;
                $retVal = "<!--LINK $ns:$key-->{$trail}";
            }
            $this->size ++;
        }
        return $retVal;
    }

    /**
     * Get the stub threshold
     */
    function getStubThreshold()
    {
        global $wgUser;
        if (! isset($this->stubThreshold))
        {
            $this->stubThreshold = $wgUser->getOption('stubthreshold');
        }
        return $this->stubThreshold;
    }

    /**
     * Replace <!--LINK--> link placeholders with actual links, in the buffer Placeholders created in
     * Skin::makeLinkObj() Returns an array of link CSS classes, indexed by PDBK.
     */
    function replace(&$text)
    {
        $colours = $this->replaceInternal($text);
        // $this->replaceInterwiki($text);
        return $colours;
    }

    /**
     * Replace internal links
     */
    protected function replaceInternal(&$text)
    {
        if (! $this->internals)
        {
            return;
        }

        $colours = [];
        $linkCache = MediawikiLinkCache::singleton();
        $output = $this->parent->getOutput();

        $threshold = 0;

        // Sort by namespace
        ksort($this->internals);

        // Generate query
        $query = false;
        $current = null;
        $title_conditions = [];

        foreach ($this->internals as $ns => $entries)
        {
            foreach ($entries as $index => $entry)
            {
                $key = "$ns:$index";
                $title = $entry['title'];
                $pdbk = $entry['pdbk'];

                // Skip invalid entries.
                // Result will be ugly, but prevents crash.
                if (is_null($title))
                {
                    continue;
                }

                // Check if it's a static known link, e.g. interwiki
                if ($title->isAlwaysKnown())
                {
                    $colours[$pdbk] = '';
                }
                elseif (($id = $linkCache->getGoodLinkID($pdbk)) != 0)
                {
                    if ($title->isRedirect())
                    {
                        // Page is a redirect
                        $colours[$pdbk] = 'mw-redirect';
                    }
                    elseif ($threshold > 0 && $title->exists() && $title->getLength() < $threshold &&
                         $title->getNamespace())
                    {
                        // Page is a stub
                        $colours[$pdbk] = 'stub';
                    }
                    $output->addLink($title, $id);
                }
                elseif ($linkCache->isBadLink($pdbk))
                {
                    $colours[$pdbk] = 'new';
                }
                else
                {
                    $title_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
                        new StaticConditionVariable($title->getText()));
                }
            }
        }

        $title_condition = new OrCondition($title_conditions);

        $wiki = $this->parent->get_mediawiki_parser_context()->get_wiki();
        // $wiki = $complex_wiki_page->get_parent_object();
        $wiki_pages = $wiki->get_wiki_pages_by_title($title_condition);
        $wiki_complex_ids = [];

        foreach($wiki_pages as $wiki_page)
        {
            $title = MediawikiTitle::makeTitle(NS_MAIN, $wiki_page->get_title());
            $pdbk = $title->getPrefixedDBkey();
            $linkCache->addGoodLinkObj($wiki_page->get_id(), $title, 1024, 0);
            $colours[$pdbk] = MediawikiLinker::getLinkColour($title, $threshold);

            $complex_wiki_page_conditions = [];
            $complex_wiki_page_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class,
                    ComplexContentObjectItem::PROPERTY_PARENT),
                new StaticConditionVariable($wiki->get_id()));
            $complex_wiki_page_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class,
                    ComplexContentObjectItem::PROPERTY_REF),
                new StaticConditionVariable($wiki_page->get_id()));

            $current_complex_wiki_page = DataManager::retrieves(
                ComplexContentObjectItem::class,
                new DataClassRetrievesParameters(new AndCondition($complex_wiki_page_conditions)))->current();
            $wiki_complex_ids[$pdbk] = $current_complex_wiki_page->get_id();
        }

        // Construct search and replace arrays
        $replacePairs = [];
        foreach ($this->internals as $ns => $entries)
        {
            foreach ($entries as $index => $entry)
            {
                $pdbk = $entry['pdbk'];
                $title = $entry['title'];
                $query = isset($entry['query']) ? $entry['query'] : '';
                $key = "$ns:$index";
                $searchkey = "<!--LINK $key-->";
                if (! isset($colours[$pdbk]) || $colours[$pdbk] == 'new')
                {
                    $linkCache->addBadLinkObj($title);
                    $colours[$pdbk] = 'new';
                    $output->addLink($title, 0);
                    $replacePairs[$searchkey] = MediawikiLinker::makeBrokenLinkObj(
                        $title,
                        $entry['text'],
                        $this->parent->get_mediawiki_parser_context()->get_parameters());
                }
                else
                {
                    $query_parameters = $this->parent->get_mediawiki_parser_context()->get_parameters();
                    $query_parameters[Manager::PARAM_ACTION] = \Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager::ACTION_VIEW_WIKI_PAGE;
                    $query_parameters[Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $wiki_complex_ids[$pdbk];

                    $replacePairs[$searchkey] = MediawikiLinker::makeColouredLinkObj(
                        $title,
                        $colours[$pdbk],
                        $entry['text'],
                        $query_parameters);
                }
            }
        }
        $replacer = new HashtableReplacer($replacePairs, 1);

        // Do the thing
        $text = preg_replace_callback('/(<!--LINK .*?-->)/', $replacer->cb(), $text);
    }

    private function linkText($target)
    {
        // We might be passed a non-Title by make*LinkObj(). Fail gracefully.
        if (! $target instanceof MediawikiTitle)
        {
            return '';
        }

        // If the target is just a fragment, with no title, we return the frag-
        // ment text. Otherwise, we return the title text itself.
        if ($target->getPrefixedText() === '' and $target->getFragment() !== '')
        {
            return htmlspecialchars($target->getFragment());
        }
        return htmlspecialchars($target->getPrefixedText());
    }

    /**
     * Replace interwiki links
     */
    protected function replaceInterwiki(&$text)
    {
        if (empty($this->interwikis))
        {
            return;
        }

        // Make interwiki link HTML
        $sk = $this->parent->getOptions()->getSkin();
        $replacePairs = [];
        foreach ($this->interwikis as $key => $link)
        {
            $replacePairs[$key] = $sk->link($link['title'], $link['text']);
        }
        $replacer = new HashtableReplacer($replacePairs, 1);

        $text = preg_replace_callback('/<!--IWLINK (.*?)-->/', $replacer->cb(), $text);
    }

    /**
     * Replace <!--LINK--> link placeholders with plain text of links (not HTML-formatted).
     *
     * @param string $text
     * @return string
     */
    function replaceText($text)
    {
        $text = preg_replace_callback('/<!--(LINK|IWLINK) (.*?)-->/', array(&$this, 'replaceTextCallback'), $text);
        return $text;
    }

    /**
     *
     * @param array $matches
     * @return string @private
     */
    function replaceTextCallback($matches)
    {
        $type = $matches[1];
        $key = $matches[2];
        if ($type == 'LINK')
        {
            list($ns, $index) = explode(':', $key, 2);
            if (isset($this->internals[$ns][$index]['text']))
            {
                return $this->internals[$ns][$index]['text'];
            }
        }
        elseif ($type == 'IWLINK')
        {
            if (isset($this->interwikis[$key]['text']))
            {
                return $this->interwikis[$key]['text'];
            }
        }
        return $matches[0];
    }
}
