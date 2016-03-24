<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Form\CourseImportForm;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_importer.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component allows the use to import a course
 */
class CourseImporterComponent extends Manager
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

        $form = new CourseImportForm(CourseImportForm :: TYPE_IMPORT, $this->get_url());

        if ($form->validate())
        {
            $success = $form->import_courses();
            $this->redirect(
                Translation :: get($success ? 'CsvCoursesProcessed' : 'CsvCoursesNotProcessed') . '<br />' .
                     $form->get_failed_csv(),
                    ($success ? false : true));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = '<div class="clear"></div><br />';
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
        $html[] = '<b>action</b>;<b>code</b>;<b>title</b>;<b>category</b>;<b>teacher</b>;<b>course_type</b>;language';
        $html[] = 'A;BIO0015;Biology;BIO;username;Curricula;en';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . Translation :: get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>Action</u></b>'; // NO translation! This field is
                                          // always plain 'action' in each CSV,
                                          // regardless the language!
        $html[] = '<br />A: ' . Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '<br />U: ' . Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '<br />D: ' . Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '</blockquote>';

        return implode($html, "\n");
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
    }
}
