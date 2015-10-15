<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass\Bookmark;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;

/**
 * Block to display all bookmarks foor the handbooks application
 *
 * @package handbook.block
 */
class WeblcmsBookmarkDisplay extends NewBlock implements ConfigurableInterface
{
    const CONFIGURATION_SHOW_EMPTY = 'show_when_empty';

    public function __construct($renderer, $block)
    {
        parent :: __construct($renderer, $block, Translation :: get('WeblcmsBookmarks'));
    }

    public function isConfigured()
    {
        // no configuration needed for now
        return true;
    }

    public function isVisible()
    {
        if ($this->isEmpty() && ! $this->showWhenEmpty())
        {
            return false;
        }
        return true; // i.e.display on homepage when anonymous
    }

    public function isHidable()
    {
        return true;
    }

    public function isDeletable()
    {
        return true;
    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    public function displayContent()
    {
        $bookmarks = $this->getBookmarks();

        while ($bookmark = $bookmarks->next_result())
        {
            $display = ContentObjectRenditionImplementation :: factory(
                $bookmark,
                ContentObjectRendition :: FORMAT_HTML,
                ContentObjectRendition :: VIEW_SHORT,
                $this);

            $html[] = $display->render();
            $html[] = '</br>';
        }
        return implode(PHP_EOL, $html);
    }

    public function showWhenEmpty()
    {
        return $this->getBlock()->getSetting(self :: CONFIGURATION_SHOW_EMPTY, true);
    }

    public function isEmpty()
    {
        return $this->get_bookmarks()->size() == 0;
    }

    public function getBookmarks()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Bookmark :: class_name(), Bookmark :: PROPERTY_APPLICATION),
            new StaticConditionVariable(\Chamilo\Application\Weblcms\Manager :: APPLICATION_NAME));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable(Session :: get_user_id()));

        $condition = new AndCondition($conditions);
        $parameters = new DataClassRetrievesParameters($condition);

        $bookmarks_resultset = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            Bookmark :: class_name(),
            $parameters);

        return $bookmarks_resultset;
    }

    /**
     *
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return array(self :: CONFIGURATION_SHOW_EMPTY);
    }
}
