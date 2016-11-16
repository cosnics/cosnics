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
                new WeblcmsRepository());
            
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
        
        $html[] = '<div style="border: solid 1px darkgray; margin: 15px; padding: 10px; background: #EFEFEF;">';
        $html[] = '<h4>' . Translation::get('Users') . '</h4>';
        $html[] = '<p>' . Translation::get('CSVMustLookLikeForUsers') . ' (' . Translation::get('MandatoryFields') .
             ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '<b>action</b>;<b>username</b>;<b>coursecode</b>;<b>status</b>';
        $html[] = 'A;jdoe;course01;Teacher';
        $html[] = 'D;a.dam;course01;Student';
        $html[] = '</pre></blockquote>';
        $html[] = '</div>';
        
        $html[] = '<div style="border: solid 1px darkgray; margin: 15px; padding: 10px; background: #EFEFEF;">';
        $html[] = '<h4>' . Translation::get('Groups') . '</h4>';
        $html[] = '<p>' . Translation::get('CSVMustLookLikeForGroups') . ' (' . Translation::get('MandatoryFields') .
             ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '<b>action</b>;<b>groupcode</b>;<b>coursecode</b>;<b>status</b>';
        $html[] = 'A;group1;course01;Teacher';
        $html[] = 'D;group2;course01;Student';
        $html[] = '</pre></blockquote>';
        $html[] = '</div>';
        
        $html[] = '<h4>' . Translation::get('Details') . '</h4>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation::get('Action') . '</u></b><br />';
        $html[] = '<br />A: ' . Translation::get('Add', null, Utilities::COMMON_LIBRARIES);
        $html[] = '<br />U: ' . Translation::get('Update', null, Utilities::COMMON_LIBRARIES);
        $html[] = '<br />D: ' . Translation::get('Delete', null, Utilities::COMMON_LIBRARIES);
        $html[] = '<br /><br /><br />';
        $html[] = '<u><b>' . Translation::get('Status') . '</u></b><br />';
        $html[] = '<br />Teacher';
        $html[] = '<br />Student';
        $html[] = '</blockquote>';
        
        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if ($this->get_user()->is_platform_admin())
        {
            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(), 
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER));
            $breadcrumbtrail->add(
                new Breadcrumb($redirect->getUrl(), Translation::get('TypeName', null, 'Chamilo\Core\Admin')));
            
            // $redirect = new Redirect(
            // array(
            // Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
            // \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_ADMIN_BROWSER,
            // DynamicTabsRenderer :: PARAM_SELECTED_TAB => ClassnameUtilities :: getInstance()->getNamespaceId(
            // self :: package())));
            // $breadcrumbtrail->add(new Breadcrumb($redirect->getUrl(), Translation :: get('Courses')));
        }
        
        $breadcrumbtrail->add_help('weblcms_course_user_importer');
    }
}
