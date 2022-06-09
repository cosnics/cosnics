<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Core\Admin\ImportActionsInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Translation\Translation;

class Manager implements ActionsSupportInterface, ImportActionsInterface
{

    public static function getActions(): Actions
    {
        $links = [];

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_TYPE_MANAGER
            )
        );
        $links[] = new Action(
            Translation::get('CourseTypeListDescription'),Translation::get('CourseTypeList'),
            new FontAwesomeGlyph('list', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_COURSE_MANAGER
            )
        );
        $links[] = new Action(
            Translation::get('ListDescription'),Translation::get('CourseList'),
            new FontAwesomeGlyph('chalkboard', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_COURSE_MANAGER,
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_CREATE
            )
        );
        $links[] = new Action(
             Translation::get('CreateDescription'),Translation::get('CreateCourse'),
            new FontAwesomeGlyph('plus', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_IMPORT_COURSES
            )
        );
        $links[] = new Action(
            Translation::get('ImportDescription'),Translation::get('Import'),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_REQUEST_BROWSER
            )
        );
        $links[] = new Action(
            Translation::get('RequestDescription'),Translation::get('RequestList'),
            new FontAwesomeGlyph('list', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_CATEGORY_MANAGER
            )
        );
        $links[] = new Action(
            Translation::get('CourseCategoryManagementDescription'),Translation::get('CourseCategoryManagement'),
            new FontAwesomeGlyph('folder', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_IMPORT_COURSE_USERS
            )
        );
        $links[] = new Action(
            Translation::get('UserImportDescription'),Translation::get('UserImport'),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_COURSE_MANAGER
            )
        );
        $info = new Actions(\Chamilo\Application\Weblcms\Manager::context(), $links, $redirect->getUrl());

        return $info;
    }

    public static function get_import_actions()
    {
        $links = [];

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_IMPORT_COURSES
            )
        );
        $links[] = new Action(
            Translation::get('ImportCoursesDescription'), Translation::get('ImportCourses'),
            new FontAwesomeGlyph('chalkboard', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_IMPORT_COURSE_USERS
            )
        );
        $links[] = new Action(
            Translation::get('UserImportDescription'),Translation::get('UserImport'),
            new FontAwesomeGlyph('users', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        return $links;
    }
}
