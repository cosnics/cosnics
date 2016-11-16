<?php
namespace Chamilo\Core\Repository\Viewer\Menu;

use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * Extension on the content object category menu in preparation of an refactoring that is needed in the tree menu's
 * where the url is asked from the parent
 * 
 * @author Sven Vanpoucke
 */
class RepositoryCategoryMenu extends ContentObjectCategoryMenu
{
    const TREE_NAME = __CLASS__;

    /**
     * The parent application component
     * 
     * @var Application
     */
    private $parent;

    /**
     * Creates a new category navigation menu.
     * 
     * @param Application $parent The parent component
     * @param int $owner The ID of the owner of the categories to provide in this menu.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *        string "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the root.
     * @param string[] $filter_count_on_types - Array to define the types on which the count on the categories should be
     *        filtered
     */
    public function __construct($parent, $owner, WorkspaceInterface $currentWorkspace, $current_category = null, 
        $url_format = '?category=%s', $extra_items = array(), $filter_count_on_types = array(), $exclude_types = array())
    {
        $this->parent = $parent;
        parent::__construct(
            $currentWorkspace, 
            $current_category, 
            $url_format, 
            $extra_items, 
            $filter_count_on_types, 
            $exclude_types);
    }

    /**
     * Gets the URL of a given category
     * 
     * @param int $category_id The id of the category
     * @return string The requested URL
     */
    protected function get_category_url($category_id)
    {
        return $this->parent->get_category_url($category_id);
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }
}
