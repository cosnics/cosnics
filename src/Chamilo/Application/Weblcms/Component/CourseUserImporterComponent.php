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
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);

/**
 * @package Chamilo\Application\Weblcms\Component
 *          Weblcms component allows the use to import course user relations
 */
class CourseUserImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageCourses');

        $form = new CourseEntityImportForm($this->get_url());

        $html = [];
        $html[] = $this->render_header();

        if ($form->validate())
        {
            try
            {
                $importers = [];

                $importers[] = new Csv(new Import());

                $courseEntityImporter = new CourseEntityImporter(
                    new ImportFormatFactory($importers), new WeblcmsRepository(), $this->getTranslator()
                );

                $file = new UploadedFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type']);

                $result = $courseEntityImporter->importCourseEntitiesFromFile($file);

                $html[] = $this->getTwig()->render(
                    'Chamilo\Application\Weblcms:CourseUserImporterResult.html.twig', ['importerResult' => $result]
                );
            }
            catch (Exception $ex)
            {
                $html[] = $this->getTwig()->render(
                    'Chamilo\Application\Weblcms:CourseUserImporter.html.twig',
                    ['form' => $form->toHtml(), 'error' => $ex->getMessage()]
                );
            }
        }
        else
        {
            $html[] = $this->getTwig()->render(
                'Chamilo\Application\Weblcms:CourseUserImporter.html.twig', ['form' => $form->toHtml()]
            );
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        if ($this->getUser()->isPlatformAdmin())
        {
            $browseUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER
                ]
            );

            $breadcrumbtrail->add(
                new Breadcrumb($browseUrl, Translation::get('TypeName', null, 'Chamilo\Core\Admin'))
            );
        }
    }
}
