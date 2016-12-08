<?php
namespace Chamilo\Configuration\Category;

use Chamilo\Configuration\Category\Interfaces\CategoryVisibilitySupported;
use Chamilo\Configuration\Category\Interfaces\ImpactViewSupport;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'category_action';
    const PARAM_CATEGORY_ID = 'category_id';
    const PARAM_CATEGORY_ID_TO_DELETE = 'delete_category_id';
    const PARAM_DIRECTION = 'direction';
    const PARAM_REMOVE_SELECTED_CATEGORIES = 'remove_selected_categories';
    const PARAM_MOVE_SELECTED_CATEGORIES = 'move_selected_categories';
    const ACTION_BROWSE_CATEGORIES = 'Browser';
    const ACTION_CREATE_CATEGORY = 'Creator';
    const ACTION_UPDATE_CATEGORY = 'Updater';
    const ACTION_DELETE_CATEGORY = 'Deleter';
    const ACTION_TOGGLE_CATEGORY_VISIBILITY = 'VisibilityToggler';
    const ACTION_MOVE_CATEGORY = 'Mover';
    const ACTION_CHANGE_CATEGORY_PARENT = 'ParentChanger';
    const ACTION_AJAX_MOVE_CATEGORIES = 'AjaxCategoryMover';
    const ACTION_AJAX_DELETE_CATEGORIES = 'AjaxCategoryDeleter';
    const ACTION_IMPACT_VIEW = 'ImpactView';
    const DEFAULT_ACTION = self :: ACTION_BROWSE_CATEGORIES;
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    public function category_visibility_supported()
    {
        return $this->get_category() instanceof CategoryVisibilitySupported;
    }

    public function get_browse_categories_url($category_id = 0)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => $category_id));
    }

    public function get_create_category_url($category_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id));
    }

    public function get_update_category_url($category_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_UPDATE_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id));
    }

    public function get_delete_category_url($category_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_DELETE_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id));
    }

    public function get_move_category_url($category_id, $direction = 1)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MOVE_CATEGORY,
                self :: PARAM_CATEGORY_ID => $category_id,
                self :: PARAM_DIRECTION => $direction));
    }

    public function get_change_category_parent_url($category_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_CHANGE_CATEGORY_PARENT,
                self :: PARAM_CATEGORY_ID => $category_id));
    }

    public function get_toggle_visibility_category_url($category_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_TOGGLE_CATEGORY_VISIBILITY,
                self :: PARAM_CATEGORY_ID => $category_id));
    }

    /**
     * Returns the url to the impact view component
     *
     * @param int | int[] $category_id
     * @return string
     */
    public function get_impact_view_url($category_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_IMPACT_VIEW, self::PARAM_CATEGORY_ID_TO_DELETE => $category_id));
    }

    public function set_subcategories_allowed($subcategories_allowed)
    {
        $this->subcategories_allowed = $subcategories_allowed;
    }

    public function get_subcategories_allowed()
    {
        return $this->subcategories_allowed;
    }

    public function supports_impact_view()
    {
        return $this->get_parent() instanceof ImpactViewSupport;
    }

    public function get_additional_parameters()
    {
        $parameters = array();
        if (! is_null(Request :: get(\Chamilo\Configuration\Category\Manager :: PARAM_CATEGORY_ID)))
        {
            $parameters[] = \Chamilo\Configuration\Category\Manager :: PARAM_CATEGORY_ID;
        }

        return array_merge($parameters, $this->get_application()->get_category_parameters());
    }

    /**
     *
     * @return Application | CategorySupport
     */
    public function get_parent()
    {
        return parent :: get_parent();
    }
}
