<?php
namespace Chamilo\Application\Weblcms\Menu;

use Chamilo\Application\Weblcms\Component\AdminRequestBrowserComponent;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Application\Weblcms\Menu
 */
class RequestsTreeRenderer extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    private $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
        $menu = $this->get_menu_items();
        parent::__construct($menu);
    }

    public function check_selected_item($menu_item, $request_type, $request_view)
    {
        if ($request_type == $this->parent->get_request_type() && $request_view == $this->parent->get_request_view())
        {
            $this->forceCurrentUrl($menu_item['url']);
        }
    }

    private function get_menu_items()
    {
        $menu = array();
        $menu_item = array();
        $glyph = new FontAwesomeGlyph('home', array(), null, 'fas');
        $menu_item['class'] = $glyph->getClassNamesString();
        $menu_item['title'] = Translation::get('Requests');
        $menu_item['description'] = Translation::get('Requests');
        $menu_item['url'] = '#';
        $menu_item['sub'] = $this->get_requests_array();
        $menu[] = $menu_item;

        return $menu;
    }

    private function get_requests_array()
    {
        $sub_menu = array();

        $menu_item = array();
        $glyph = new FontAwesomeGlyph('user', array(), null, 'fas');
        $menu_item['class'] = $glyph->getClassNamesString();
        $menu_item['title'] = Translation::get('SubscriptionRequests');
        $menu_item['description'] = Translation::get('SubscriptionRequests');
        $menu_item['url'] = '#';
        $menu_item['sub'] = $this->get_sub_division(CommonRequest::SUBSCRIPTION_REQUEST);
        $sub_menu[] = $menu_item;

        return $sub_menu;
    }

    private function get_sub_division($request_type)
    {
        $sub_menu = array();

        $request_database_method = null;
        switch ($request_type)
        {
            case CommonRequest::SUBSCRIPTION_REQUEST :
                $request_database_method = 'count_requests';
                break;
            case CommonRequest::CREATION_REQUEST :
                $request_database_method = 'count_course_create_requests';
                break;
        }

        $request_view = null;
        $translation = null;
        $condition = null;
        $class = null;

        for ($i = 0; $i < 3; $i ++)
        {
            switch ($i)
            {
                case 0 :
                    $translation = 'Pending';
                    $glyph = new FontAwesomeGlyph('pause-circle', array(), null, 'fas');
                    $class = $glyph->getClassNamesString();
                    $request_view = AdminRequestBrowserComponent::PENDING_REQUEST_VIEW;
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(CourseRequest::class_name(), CourseRequest::PROPERTY_DECISION),
                        new StaticConditionVariable(CourseRequest::NO_DECISION)
                    );
                    break;
                case 1 :
                    $translation = 'Allowed';
                    $glyph = new FontAwesomeGlyph('check-square', array(), null, 'fas');
                    $class = $glyph->getClassNamesString();
                    $request_view = AdminRequestBrowserComponent::ALLOWED_REQUEST_VIEW;
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(CourseRequest::class_name(), CourseRequest::PROPERTY_DECISION),
                        new StaticConditionVariable(CourseRequest::ALLOWED_DECISION)
                    );
                    break;
                case 2 :
                    $translation = 'Denied';
                    $glyph = new FontAwesomeGlyph('times-circle', array(), null, 'fas');
                    $class = $glyph->getClassNamesString();
                    $request_view = AdminRequestBrowserComponent::DENIED_REQUEST_VIEW;
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(CourseRequest::class_name(), CourseRequest::PROPERTY_DECISION),
                        new StaticConditionVariable(CourseRequest::DENIED_DECISION)
                    );
                    break;
            }

            $count = $this->parent->$request_database_method($condition);

            $menu_item = array();
            $menu_item['class'] = $class;
            $menu_item['title'] = Translation::get($translation) . ' (' . $count . ')';
            $menu_item['description'] = Translation::get($translation);
            $menu_item['url'] = $this->parent->get_url(
                array(Manager::PARAM_REQUEST_TYPE => $request_type, Manager::PARAM_REQUEST_VIEW => $request_view)
            );
            $this->check_selected_item($menu_item, $request_type, $request_view);
            $sub_menu[] = $menu_item;
        }

        return $sub_menu;
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }

    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');

        return $renderer->toHTML();
    }
}
