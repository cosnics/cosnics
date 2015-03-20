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

/**
 * Block to display all bookmarks foor the handbooks application
 * 
 * @package handbook.block
 */
class WeblcmsBookmarkDisplay extends NewBlock
{

    public function __construct($parent, $block_info, $configuration)
    {
        parent :: __construct($parent, $block_info, $configuration, Translation :: get('WeblcmsBookmarks'));
    }

    public function is_configured()
    {
        // no configuration needed for now
        return true;
    }

    public function is_visible()
    {
        if ($this->is_empty() && ! $this->show_when_empty())
        {
            return false;
        }
        return true; // i.e.display on homepage when anonymous
    }

    public function is_hidable()
    {
        return true;
    }

    public function is_deletable()
    {
        return true;
    }

    /**
     * Returns the html to display when the block is configured.
     * 
     * @return string
     */
    public function display_content()
    {
        $bookmarks_resultset = $this->get_bookmarks();
        
        while ($bookmarks_resultset && $bm = $bookmarks_resultset->next_result())
        {
            $display = ContentObjectRenditionImplementation :: factory(
                $bm, 
                ContentObjectRendition :: FORMAT_HTML, 
                ContentObjectRendition :: VIEW_SHORT, 
                $this);
            $html[] = $display->render();
            $html[] = '</br>';
        }
        return implode(PHP_EOL, $html);
    }

    public function show_when_empty()
    {
        $configuration = $this->get_configuration();
        $result = isset($configuration['show_when_empty']) ? $configuration['show_when_empty'] : true;
        $result = (bool) $result;
        return $result;
    }

    public function is_empty()
    {
        $bookmarks = $this->get_bookmarks();
        return $bookmarks->size() == 0;
    }

    public function get_bookmarks()
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

    public function count_data()
    {
    }

    public function get_views()
    {
    }

    public function retrieve_data()
    {
    }
}
