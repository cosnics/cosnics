<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass\Bookmark;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Block to display all bookmarks foor the handbooks application
 *
 * @package handbook.block
 */
class WeblcmsBookmarkDisplay extends Block implements ConfigurableInterface
{
    public const CONFIGURATION_SHOW_EMPTY = 'show_when_empty';

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    public function displayContent()
    {
        $bookmarks = $this->getBookmarks();

        foreach ($bookmarks as $bookmark)
        {
            $display = ContentObjectRenditionImplementation::factory(
                $bookmark, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_SHORT, $this
            );

            $html[] = $display->render();
            $html[] = '</br>';
        }

        return implode(PHP_EOL, $html);
    }

    public function getBookmarks()
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Bookmark::class, Bookmark::PROPERTY_APPLICATION),
            new StaticConditionVariable(Manager::CONTEXT)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getSessionUtilities()->getUserId())
        );

        $condition = new AndCondition($conditions);
        $parameters = new DataClassRetrievesParameters($condition);

        $bookmarks_resultset = DataManager::retrieve_active_content_objects(
            Bookmark::class, $parameters
        );

        return $bookmarks_resultset;
    }

    /**
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return [self::CONFIGURATION_SHOW_EMPTY];
    }

    public function isConfigured()
    {
        // no configuration needed for now
        return true;
    }

    public function isDeletable()
    {
        return true;
    }

    public function isEmpty()
    {
        return $this->getBookmarks()->count() == 0;
    }

    public function isHidable()
    {
        return true;
    }

    public function isVisible()
    {
        if ($this->isEmpty() && !$this->showWhenEmpty())
        {
            return false;
        }

        return true; // i.e.display on homepage when anonymous
    }

    public function showWhenEmpty()
    {
        return $this->getBlock()->getSetting(self::CONFIGURATION_SHOW_EMPTY, true);
    }
}
