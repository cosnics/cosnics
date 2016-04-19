<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Cron\Storage\DataClass\ExportJob;
use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Export\Storage\DataClass\Export;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

class ExportComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);
        $ids = Request :: get(self :: PARAM_EXPORT_TEMPLATE_ID);
        $error = false;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $cron_enabled = PlatformSetting :: get('enable_export_cron_job', 'Chamilo\Application\Survey');

                $export_template = DataManager :: retrieve_export_template_by_id($id);

                $args = array();
                $args[Export :: PROPERTY_EXPORT_REGISTRATION_ID] = $export_template->get_export_registration_id();
                $args[Export :: PROPERTY_USER_ID] = $this->get_user_id();
                $args[Export :: PROPERTY_TEMPLATE_NAME] = $export_template->get_name();
                $args[Export :: PROPERTY_TEMPLATE_DESCRIPTION] = $export_template->get_description();
                $args[Export :: PROPERTY_SURVEY_PUBLICATION_ID] = $export_template->get_publication_id();
                $args[Export :: PROPERTY_STATUS] = Export :: STATUS_EXPORT_NOT_CREATED;
                $args[Export :: PROPERTY_FINISHED] = 0;
                $args[Export :: PROPERTY_EXPORT_JOB_ID] = 0;

                $export_trackers = Event :: trigger(
                    Export :: REGISTER_PUBLICATION_EXPORT_EVENT,
                    Manager :: package(),
                    $args);
                $export_tracker = $export_trackers[0];

                if (! $cron_enabled)
                {

                    $export_type = 'xlsx';
                    $export = Exporter :: factory($export_template, $publication_id);
                    $file = $export->save();

                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        Registration :: PROPERTY_TYPE,
                        Registration :: TYPE_CONTENT_OBJECT);
                    $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, File :: get_type_name());
                    $conditions[] = new EqualityCondition(Registration :: PROPERTY_STATUS, true);
                    $condition = new AndCondition($conditions);

                    $registration = \Chamilo\Core\Admin\Storage\DataManager :: count_registrations($condition);
                    if ($registration > 0)
                    {
                        $html_object = new File();
                        $html_object->set_title($export_template->get_name());
                        $html_object->set_description($export_template->get_description());
                        $html_object->set_parent_id(0);
                        $html_object->set_owner_id($this->get_user_id());
                        $html_object->set_filename($export->get_file_name() . '.' . $export_type);

                        $html_object->set_in_memory_file($file);

                        if (! $html_object->create())
                        {
                            $export_tracker->set_status(Export :: STATUS_EXPORT_NOT_CREATED);
                            $export_tracker->set_finished(time());
                            $export_tracker->update();
                            $message = 'ObjectNotCreated';
                            $error = true;
                        }
                        else
                        {

                            $export_tracker->set_status(Export :: STATUS_EXPORT_CREATED);
                            $export_tracker->set_finished(time());
                            $export_tracker->update();
                            $message = 'SavedToRepository';
                            $error = false;
                        }
                    }
                    else
                    {
                        $export_tracker->set_status(Export :: STATUS_EXPORT_NOT_CREATED);
                        $export_tracker->set_finished(time());
                        $export_tracker->update();
                        $message = 'DocumentNotAvailable';
                        $error = true;
                    }

                    $tab = BrowserComponent :: TAB_EXPORT_TEMPLATES;
                }
                else
                {
                    $export_job = new ExportJob();
                    $export_job->set_user_id($this->get_user_id());
                    $export_job->set_publication_id($publication_id);
                    $export_job->set_export_template_id($id);
                    $export_job->set_status(ExportJob :: STATUS_NEW);
                    $export_job->set_export_type(ExportJob :: EXPORT_TYPE_TEMPLATE_EXPORT);
                    $export_job->set_UUID(0);

                    if (! $export_job->create())
                    {
                        $export_tracker->set_status(Export :: STATUS_EXPORT_NOT_CREATED);

                        $export_tracker->update();
                        $message = 'ExportJobNotCreated';
                        $error = true;
                    }
                    else
                    {
                        $export_tracker->set_status(Export :: STATUS_EXPORT_IN_QUEUE);
                        $export_tracker->set_export_job_id($export_job->get_id());
                        $export_tracker->update();
                        $message = 'ExportJobCreated';
                        $error = false;
                    }
                }

                $tab = BrowserComponent :: TAB_EXPORT_TACKERS;
            }
            $this->redirect(
                Translation :: get($message),
                $error,
                array(
                    self :: PARAM_ACTION => self :: ACTION_BROWSE,
                    Manager :: PARAM_PUBLICATION_ID => $publication_id,
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoExportTemplateSelected')));
        }
    }
}
?>