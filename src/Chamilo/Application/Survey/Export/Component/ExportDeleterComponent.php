<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Export\Storage\DataClass\Export;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ExportDeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);
        $ids = $this->getRequest()->get(self :: PARAM_EXPORT_TRACKER_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            if (RightsService :: getInstance())
            {
                foreach ($ids as $id)
                {

                    $export = DataManager :: retrieve_by_id(Export :: class_name(), $id);

                    if ($export->get_status() == Export :: STATUS_EXPORT_IN_QUEUE)
                    {
                        if (! $export->delete())
                        {
                            $failures ++;
                        }
                    }
                    else
                    {
                        $failures ++;
                    }
                }
            }
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedExportTrackerNotDeleted';
                }
                else
                {
                    $message = 'SelectedExportTrackersNotDeleted';
                }
                $tab = BrowserComponent :: TAB_EXPORT_TACKERS;
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedExportTrackerDeleted';
                }
                else
                {
                    $message = 'SelectedExportTrackersDeleted';
                }
                $tab = BrowserComponent :: TAB_EXPORT_TACKERS;
            }

            $this->redirect(
                Translation :: get($message),
                ($failures ? true : false),
                array(
                    self :: PARAM_ACTION => self :: ACTION_BROWSE,
                    Manager :: PARAM_PUBLICATION_ID => $publication_id,
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoExportTrackersSelected')));
        }
    }
}
?>