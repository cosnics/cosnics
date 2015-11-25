<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Form\CourseUserImportForm;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * $Id: course_user_importer.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component allows the use to import course user relations
 */
class CourseUserImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        $form = new CourseUserImportForm(CourseUserImportForm :: TYPE_IMPORT, $this->get_url());

        if ($form->validate())
        {
            $success = $form->import_course_users();
            $this->redirect(
                Translation :: get($success ? 'CsvUsersProcessed' : 'CsvUsersNotProcessed') . '<br />' .
                     $form->get_failed_csv(),
                    ($success ? false : true));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->display_extra_information();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function display_extra_information()
    {
        $html = array();
        $html[] = '<p>' . Translation :: get('CSVMustLookLike') . ' (' . Translation :: get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '<b>action</b>;<b>username</b>;<b>coursecode</b>;<b>status</b>';
        $html[] = 'A;jdoe;course01;1';
        $html[] = 'D;a.dam;course01;5';
        $html[] = '</pre></blockquote>';
        $html[] = '<p>' . Translation :: get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation :: get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '<br />U: ' . Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '<br />D: ' . Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '<br /><br />';
        $html[] = '<u><b>' . Translation :: get('Status') . '</u></b>';
        $html[] = '<br />1: ' . Translation :: get('Teacher');
        $html[] = '<br />5: ' . Translation :: get('Student');
        $html[] = '</blockquote>';

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if ($this->get_user()->is_platform_admin())
        {
            $redirect = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                    \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_ADMIN_BROWSER));
            $breadcrumbtrail->add(
                new Breadcrumb($redirect->getUrl(), Translation :: get('TypeName', null, 'Chamilo\Core\Admin')));

            $redirect = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                    \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_ADMIN_BROWSER,
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => ClassnameUtilities :: getInstance()->getNamespaceId(
                        self :: package())));
            $breadcrumbtrail->add(new Breadcrumb($redirect->getUrl(), Translation :: get('Courses')));
        }

        $breadcrumbtrail->add_help('weblcms_course_user_importer');
    }
}
