<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribeBrowserComponent;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Menu\TreeMenu\GenericTree;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User
 */
class PlatformgroupMenuRenderer extends GenericTree
{

    const NODE_CLASS = 'category';

    const ROOT_NODE_CLASS = 'home';

    const TREE_NAME = __CLASS__;

    private $browser;

    public function __construct($browser, $root_ids, $fake_root = false)
    {
        $this->browser = $browser;
        parent::__construct($fake_root, $root_ids);
    }

    /**
     * Returns the default node id
     *
     * @return int
     */
    protected function getDefaultNodeId()
    {
        return $this->root_ids[0];
    }

    /**
     * Returns the current node id.
     *
     * @return int
     */
    public function get_current_node_id()
    {
        $currentNodeId = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP);
        $currentNodeId = !is_null($currentNodeId) ? $currentNodeId : $this->getDefaultNodeId();

        return $currentNodeId;
    }

    /**
     * @param $node_id
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function get_node($node_id)
    {
        return DataManager::retrieve_by_id(Group::class, $node_id);
    }

    /**
     * Returns the nodes below the given parent(_id).
     *
     * @param $parent_node_id
     *
     * @return  \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function get_node_children($parent_node_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parent_node_id)
        );

        // fetch groups
        $parameters = new DataClassRetrievesParameters(
            $condition, null, null,
            new OrderBy(array(new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))))
        );

        return DataManager::retrieves(Group::class, $parameters);
    }

    public function get_node_class($node)
    {
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        return $glyph->getClassNamesString();
    }

    public function get_node_id($node)
    {
        return $node->get_id();
    }

    public function get_node_parent($node)
    {
        return $node->get_parent();
    }

    public function get_node_safe_title($node)
    {
        return strip_tags($node->get_fully_qualified_name());
    }

    public function get_node_title($node)
    {
        return $node->get_name();
    }

    /**
     * Returns the url of a node
     *
     * @param $node_id int
     *
     * @return string
     */
    public function get_node_url($node_id)
    {
        $params = [];
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_GROUP] = $node_id;
        $params[Manager::PARAM_TAB] = UnsubscribeBrowserComponent::TAB_PLATFORM_GROUPS_USERS;

        return $this->browser->get_url($params);
    }

    public function get_root_node_class()
    {
        $glyph = new FontAwesomeGlyph('home', [], null, 'fas');

        return $glyph->getClassNamesString();
    }

    public function get_root_node_title()
    {
        return Translation::get('Course', null, StringUtilities::LIBRARIES);
    }

    /**
     * Returns the url to the xml feed.
     *
     * @return string
     */
    public function get_search_url()
    {
        $searchUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Group\Ajax\Manager::CONTEXT,
                \Chamilo\Core\Group\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Group\Ajax\Manager::ACTION_XML_GROUP_MENU_FEED
            )
        );

        return $searchUrl->getUrl();
    }

    public function get_url_format()
    {
        $url_format = '?application=weblcms';
        $url_format .= '&' . \Chamilo\Application\Weblcms\Manager::PARAM_COURSE . '=' .
            Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        $url_format .= '&' . \Chamilo\Application\Weblcms\Manager::PARAM_ACTION . '=' .
            Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_ACTION);
        $url_format .= '&' . \Chamilo\Application\Weblcms\Manager::PARAM_TOOL . '=' .
            Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL);
        $url_format .= '&' . \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION . '=' .
            Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION);
        $url_format .= '&' . Manager::PARAM_BROWSER_TYPE . '=' . Request::get(Manager::PARAM_BROWSER_TYPE);
        $url_format .= '&' . Manager::PARAM_TAB . '=' . UnsubscribeBrowserComponent::TAB_PLATFORM_GROUPS_USERS;
        $url_format .= '&' . \Chamilo\Application\Weblcms\Manager::PARAM_GROUP . '=%s';

        return $url_format;
    }

    /**
     * Returns if the node has children.
     *
     * @param $node_id int The node id
     *
     * @return boolean
     */
    public function node_has_children($node_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($node_id)
        );

        return (DataManager::count(
                Group::class, new DataClassCountParameters($condition)
            ) > 0);
    }
}
