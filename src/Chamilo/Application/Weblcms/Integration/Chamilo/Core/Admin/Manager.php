<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Core\Admin\ImportActionsInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

class Manager implements ActionsSupportInterface, ImportActionsInterface
{

    public static function get_actions()
    {
        $links = array();
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_TYPE_MANAGER));
        $links[] = new DynamicAction(
            Translation::get('CourseTypeList'), 
            Translation::get('CourseTypeListDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/List'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_COURSE_MANAGER));
        $links[] = new DynamicAction(
            Translation::get('CourseList'), 
            Translation::get('ListDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/List'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_COURSE_MANAGER, 
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_CREATE));
        $links[] = new DynamicAction(
            Translation::get('CreateCourse'), 
            Translation::get('CreateDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Add'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_IMPORT_COURSES));
        $links[] = new DynamicAction(
            Translation::get('Import'), 
            Translation::get('ImportDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_REQUEST_BROWSER));
        $links[] = new DynamicAction(
            Translation::get('RequestList'), 
            Translation::get('RequestDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/List'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_CATEGORY_MANAGER));
        $links[] = new DynamicAction(
            Translation::get('CourseCategoryManagement'), 
            Translation::get('CourseCategoryManagementDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Category'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_IMPORT_COURSE_USERS));
        $links[] = new DynamicAction(
            Translation::get('UserImport'), 
            Translation::get('UserImportDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_COURSE_MANAGER));
        $info = new Actions(\Chamilo\Application\Weblcms\Manager::context(), $links, $redirect->getUrl());
        
        return $info;
    }

    public static function get_import_actions()
    {
        $links = array();
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_IMPORT_COURSES));
        $links[] = new DynamicAction(
            Translation::get('ImportCourses'), 
            Translation::get('ImportCoursesDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_IMPORT_COURSE_USERS));
        $links[] = new DynamicAction(
            Translation::get('UserImport'), 
            Translation::get('UserImportDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'), 
            $redirect->getUrl());
        
        return $links;
    }
}
