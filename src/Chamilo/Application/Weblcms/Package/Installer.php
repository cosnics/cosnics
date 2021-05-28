<?php
namespace Chamilo\Application\Weblcms\Package;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.install
 */

/**
 * This installer can be used to create the storage structure for the weblcms application.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Runs the install-script.
     */
    public function extra()
    {
        if (! $this->create_courses_subtree())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, 
                Translation::get(
                    'ObjectCreated', 
                    array('OBJECT' => Translation::get('CoursesTree')), 
                    Utilities::COMMON_LIBRARIES));
        }
        if (! $this->create_default_categories_in_weblcms())
        {
            return false;
        }
        
        if (! CourseSettingsController::install_course_settings($this))
        {
            return false;
        }
        
        if (! \Chamilo\Application\Weblcms\Request\Rights\Rights::getInstance()->create_request_root())
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, Translation::get('QuotaLocationCreated'));
        }
        
        return true;
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Installs the root location of the courses subtree and sets the default rights for everyone on root location so
     * the rights are no issue when using no course type
     * 
     * @return boolean
     */
    private function create_courses_subtree()
    {
        $rights_utilities = CourseManagementRights::getInstance();
        
        $location = $rights_utilities->create_subtree_root_location(\Chamilo\Application\Weblcms\Manager::context(),0, WeblcmsRights::TREE_TYPE_COURSE, true);
        
        if (! $location)
        {
            return false;
        }
        
        $specific_rights = $rights_utilities->get_specific_course_management_rights();
        
        foreach ($specific_rights as $right_id)
        {
            
            if (! $rights_utilities->invert_location_entity_right(\Chamilo\Application\Weblcms\Manager::context(),$right_id, 0, 0, $location->get_id()))
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Installs example course categories
     * 
     * @return boolean
     */
    private function create_default_categories_in_weblcms()
    {
        // Creating Language Skills
        $cat = new CourseCategory();
        $cat->set_name('Language skills');
        $cat->set_parent('0');
        $cat->set_display_order(1);
        
        if (! $cat->create())
        {
            return false;
        }
        
        // creating PC Skills
        $cat = new CourseCategory();
        $cat->set_name('PC skills');
        $cat->set_parent('0');
        $cat->set_display_order(1);
        if (! $cat->create())
        {
            return false;
        }
        
        // creating Projects
        $cat = new CourseCategory();
        $cat->set_name('Projects');
        $cat->set_parent('0');
        $cat->set_display_order(1);
        if (! $cat->create())
        {
            return false;
        }
        
        return true;
    }

    /**
     * Returns the list with extra installable packages that are connected to this package
     *
     * @return array
     */
    public static function get_additional_packages()
    {
        return array(
            'Chamilo\Application\Weblcms\Bridge\Assignment',
            'Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment'
        );
    }
}
