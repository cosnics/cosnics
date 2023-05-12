<?php
namespace Chamilo\Configuration\Category;

use Chamilo\Configuration\Category\Interfaces\CategoryManagerSupport;
use Chamilo\Configuration\Category\Interfaces\ImpactViewSupport;
use Chamilo\Configuration\Category\Service\CategoryManagerImplementerInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;
use Exception;

abstract class Manager extends Application
{
    public const ACTION_AJAX_DELETE_CATEGORIES = 'AjaxCategoryDeleter';
    public const ACTION_AJAX_MOVE_CATEGORIES = 'AjaxCategoryMover';
    public const ACTION_BROWSE_CATEGORIES = 'Browser';
    public const ACTION_CHANGE_CATEGORY_PARENT = 'ParentChanger';
    public const ACTION_CREATE_CATEGORY = 'Creator';
    public const ACTION_DELETE_CATEGORY = 'Deleter';
    public const ACTION_IMPACT_VIEW = 'ImpactView';
    public const ACTION_MOVE_CATEGORY = 'Mover';
    public const ACTION_TOGGLE_CATEGORY_VISIBILITY = 'VisibilityToggler';
    public const ACTION_UPDATE_CATEGORY = 'Updater';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_BROWSE_CATEGORIES;

    public const PARAM_ACTION = 'category_action';
    public const PARAM_CATEGORY_ID = 'category_id';
    public const PARAM_DIRECTION = 'direction';

    public const PROPERTY_DISPLAY_ORDER = 'display_order';

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        if (!is_null(Request::get(Manager::PARAM_CATEGORY_ID)))
        {
            $additionalParameters[] = Manager::PARAM_CATEGORY_ID;
        }

        $additionalParameters = array_merge($additionalParameters, $this->get_application()->get_category_parameters());

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * @throws \Exception
     */
    public function getCategoryManagerImplementer(): CategoryManagerImplementerInterface
    {
        $application = $this->get_application();

        if ($application instanceof CategoryManagerSupport)
        {
            return $application->getCategoryManagerImplementer();
        }
        else
        {
            throw new Exception(
                'Application must implement the CategoryManagerSupport interface and provide a CategoryManagerImplementer'
            );
        }
    }

    public function get_browse_categories_url($category_id = 0)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES, self::PARAM_CATEGORY_ID => $category_id]
        );
    }

    public function get_change_category_parent_url($category_id)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_CHANGE_CATEGORY_PARENT, self::PARAM_CATEGORY_ID => $category_id]
        );
    }

    public function get_create_category_url($category_id)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_CREATE_CATEGORY, self::PARAM_CATEGORY_ID => $category_id]
        );
    }

    public function get_delete_category_url($category_id)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_DELETE_CATEGORY, self::PARAM_CATEGORY_ID => $category_id]
        );
    }

    /**
     * Returns the url to the impact view component
     *
     * @param int | int[] $category_id
     *
     * @return string
     */
    public function get_impact_view_url($category_id)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_IMPACT_VIEW, self::PARAM_CATEGORY_ID => $category_id]
        );
    }

    public function get_move_category_url($category_id, $direction = 1)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_MOVE_CATEGORY,
                self::PARAM_CATEGORY_ID => $category_id,
                self::PARAM_DIRECTION => $direction
            ]
        );
    }

    public function get_subcategories_allowed()
    {
        return $this->subcategories_allowed;
    }

    public function get_toggle_visibility_category_url($category_id)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_TOGGLE_CATEGORY_VISIBILITY,
                self::PARAM_CATEGORY_ID => $category_id
            ]
        );
    }

    public function get_update_category_url($category_id)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_UPDATE_CATEGORY, self::PARAM_CATEGORY_ID => $category_id]
        );
    }

    public function set_subcategories_allowed($subcategories_allowed)
    {
        $this->subcategories_allowed = $subcategories_allowed;
    }

    public function supports_impact_view()
    {
        return $this->get_parent() instanceof ImpactViewSupport;
    }
}
