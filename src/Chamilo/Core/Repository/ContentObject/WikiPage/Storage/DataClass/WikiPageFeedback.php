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
    public const CONTEXT = WikiPage::CONTEXT;

    public const PROPERTY_WIKI_PAGE_ID = 'wiki_page_id';

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_WIKI_PAGE_ID]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_wiki_page_feedback';
    }

    public function getWikiPageId()
    {
        return $this->getDefaultProperty(self::PROPERTY_WIKI_PAGE_ID);
    }

    public function setWikiPageId($wikiPageId)
    {
        $this->setDefaultProperty(self::PROPERTY_WIKI_PAGE_ID, $wikiPageId);
    }
}