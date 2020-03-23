<?php
namespace Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;

/**
 * Feedback for a wiki page
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WikiPageFeedback extends Feedback
{
    const PROPERTY_WIKI_PAGE_ID = 'wiki_page_id';

    /**
     * Get the default properties of all feedback
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_WIKI_PAGE_ID));
    }

    public function getWikiPageId()
    {
        return $this->get_default_property(self::PROPERTY_WIKI_PAGE_ID);
    }

    public function setWikiPageId($wikiPageId)
    {
        $this->set_default_property(self::PROPERTY_WIKI_PAGE_ID, $wikiPageId);
    }
}