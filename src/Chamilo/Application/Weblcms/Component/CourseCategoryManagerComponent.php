<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Form\WeblcmsCategoryForm;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Category\Interfaces\CategorySupport;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Translation\Translation;

/**
 * Weblcms component allows the user to manage course categories
 */
class CourseCategoryManagerComponent extends Manager implements BreadcrumbLessComponentInterface, CategorySupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageCourses');

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Configuration\Category\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        if ($this->get_user()->isPlatformAdmin())
        {
            $typeUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER
                ]
            );
            $breadcrumbtrail->add(
                new Breadcrumb($typeUrl, Translation::get('TypeName', [], 'Chamilo\Core\Admin'))
            );

            $coursesUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER,
                    GenericTabsRenderer::PARAM_SELECTED_TAB => ClassnameUtilities::getInstance()->getNamespaceId(
                        Manager::CONTEXT
                    )
                ]
            );
            $breadcrumbtrail->add(new Breadcrumb($coursesUrl, Translation::get('Courses')));
        }
    }

    public function allowed_to_add_category($parent_category_id)
    {
        return true;
    }

    public function allowed_to_change_category_visibility($category_id)
    {
        return true;
    }

    public function allowed_to_delete_category($category_id)
    {
        return true;
    }

    public function allowed_to_edit_category($category_id)
    {
        return true;
    }

    public function count_categories($condition = null)
    {
        return DataManager::count(CourseCategory::class, new DataClassCountParameters($condition));
    }

    // Runs through dataclass

    public function getCategory()
    {
        return new CourseCategory();
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::allowed_to_delete_category()
     */

    public function get_category_form()
    {
        return new WeblcmsCategoryForm();
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::allowed_to_edit_category()
     */

    public function get_category_parameters()
    {
        return [];
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::allowed_to_change_category_visibility()
     */

    public function get_next_category_display_order($parent_id)
    {
        return null;
    }

    public function retrieve_categories($condition, $offset, $count, $order_property)
    {
        return DataManager::retrieves(
            CourseCategory::class, new DataClassRetrievesParameters($condition, $count, $offset, $order_property)
        );
    }
}
