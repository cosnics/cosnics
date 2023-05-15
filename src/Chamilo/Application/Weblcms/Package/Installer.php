<?php
namespace Chamilo\Application\Weblcms\Package;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Rights;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Installs the root location of the courses subtree and sets the default rights for everyone on root location so
     * the rights are no issue when using no course type
     *
     * @return bool
     */
    private function create_courses_subtree()
    {
        $rights_utilities = CourseManagementRights::getInstance();

        $location =
            $rights_utilities->create_subtree_root_location(Manager::CONTEXT, 0, WeblcmsRights::TREE_TYPE_COURSE, true);

        if (!$location)
        {
            return false;
        }

        $specific_rights = $rights_utilities->get_specific_course_management_rights();

        foreach ($specific_rights as $right_id)
        {

            if (!$rights_utilities->invert_location_entity_right(
                Manager::CONTEXT, $right_id, 0, 0, $location->get_id()
            ))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Installs example course categories
     *
     * @return bool
     */
    private function create_default_categories_in_weblcms()
    {
        // Creating Language Skills
        $cat = new CourseCategory();
        $cat->set_name('Language skills');
        $cat->set_parent('0');
        $cat->set_display_order(1);

        if (!$cat->create())
        {
            return false;
        }

        // creating PC Skills
        $cat = new CourseCategory();
        $cat->set_name('PC skills');
        $cat->set_parent('0');
        $cat->set_display_order(1);
        if (!$cat->create())
        {
            return false;
        }

        // creating Projects
        $cat = new CourseCategory();
        $cat->set_name('Projects');
        $cat->set_parent('0');
        $cat->set_display_order(1);
        if (!$cat->create())
        {
            return false;
        }

        return true;
    }

    /**
     * Runs the install-script.
     */
    public function extra()
    {
        if (!$this->create_courses_subtree())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, Translation::get(
                'ObjectCreated', ['OBJECT' => Translation::get('CoursesTree')], StringUtilities::LIBRARIES
            )
            );
        }
        if (!$this->create_default_categories_in_weblcms())
        {
            return false;
        }

        if (!CourseSettingsController::install_course_settings($this))
        {
            return false;
        }

        if (!Rights::getInstance()->create_request_root())
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, Translation::get('QuotaLocationCreated'));
        }

        return true;
    }
}
