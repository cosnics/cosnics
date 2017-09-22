<?php

namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Form\CourseEntityImportForm;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Service\Import\CourseEntity\CourseEntityImporter;
use Chamilo\Application\Weblcms\Service\Import\CourseEntity\Format\Csv;
use Chamilo\Application\Weblcms\Service\Import\CourseEntity\Format\ImportFormatFactory;
use Chamilo\Application\Weblcms\Storage\Repository\WeblcmsRepository;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Import;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\HttpFoundation\File\UploadedFile;

ini_set("max_execution_time", - 1);
ini_set("memory_limit", - 1);

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
        $this->checkAuthorization(Manager::context(), 'ManageCourses');

        $form = new CourseEntityImportForm($this->get_url());

        if ($form->validate())
        {
            $importers = array();

            $importers[] = new Csv(new Import());

            $courseEntityImporter = new CourseEntityImporter(
                new ImportFormatFactory($importers),
                new WeblcmsRepository()
            );

            $file = new UploadedFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type']);

            try
            {
                $courseEntityImporter->importCourseEntitiesFromFile($file);
                $success = true;
            }
            catch (\Exception $ex)
            {
                $success = false;
                $failedMessage = $ex->getMessage();
            }

            $this->redirect(
                Translation::get($success ? 'CsvUsersProcessed' : 'CsvUsersNotProcessed') . '<br />' . $failedMessage,
                ($success ? false : true)
            );
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();

            $html[] = $this->getTwig()->render(
                'Chamilo\Application\Weblcms:CourseUserImporter.html.twig', ['form' => $form->toHtml()]
            );

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if ($this->getUser()->is_platform_admin())
        {
            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER
                )
            );
            $breadcrumbtrail->add(
                new Breadcrumb($redirect->getUrl(), Translation::get('TypeName', null, 'Chamilo\Core\Admin'))
            );
        }

        $breadcrumbtrail->add_help('weblcms_course_user_importer');
    }
}
