<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Form\CourseImportForm;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
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
        $this->checkAuthorization(Manager::CONTEXT, 'ManageCourses');

        $form = new CourseImportForm(CourseImportForm::TYPE_IMPORT, $this->get_url());

        if ($form->validate())
        {
            $success = $form->import_courses();
            $this->redirectWithMessage(
                Translation::get($success ? 'CsvCoursesProcessed' : 'CsvCoursesNotProcessed') . '<br />' .
                $form->get_failed_csv(), !$success
            );
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = '<div class="clearfix"></div><br />';
            $html[] = $form->toHtml();
            $html[] = $this->display_extra_information();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        if ($this->get_user()->isPlatformAdmin())
        {
            $browserUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER
                ]
            );
            $breadcrumbtrail->add(
                new Breadcrumb($browserUrl, Translation::get('TypeName', null, 'Chamilo\Core\Admin'))
            );

            $browserTabUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER,
                    GenericTabsRenderer::PARAM_SELECTED_TAB => ClassnameUtilities::getInstance()->getNamespaceId(
                        Manager::CONTEXT
                    )
                ]
            );
            $breadcrumbtrail->add(new Breadcrumb($browserTabUrl, Translation::get('Courses')));
        }
    }

    public function display_extra_information()
    {
        $html = [];
        $html[] = '<p>' . Translation::get('CSVMustLookLike') . ' (' . Translation::get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '<b>action</b>;<b>code</b>;<b>title</b>;<b>category</b>;<b>teacher</b>;<b>course_type</b>;language';
        $html[] = 'A;BIO0015;Biology;BIO;username;Curricula;en';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . Translation::get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>Action</u></b>'; // NO translation! This field is
        // always plain 'action' in each CSV,
        // regardless the language!
        $html[] = '<br />A: ' . Translation::get('Add', null, StringUtilities::LIBRARIES);
        $html[] = '<br />U: ' . Translation::get('Update', null, StringUtilities::LIBRARIES);
        $html[] = '<br />D: ' . Translation::get('Delete', null, StringUtilities::LIBRARIES);
        $html[] = '</blockquote>';

        return implode(PHP_EOL, $html);
    }
}
