<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);
        $ids = $this->getRequest()->get(self :: PARAM_EXPORT_TEMPLATE_ID);
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
                    $template = DataManager :: retrieve_export_template_by_id($id);

                    if (! $template->delete())
                    {
                        $failures ++;
                    }
                }
            }
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedExportTemplateNotDeactivated';
                }
                else
                {
                    $message = 'SelectedExportTemplatesNotDeactivated';
                }
                $tab = BrowserComponent :: TAB_EXPORT_TEMPLATES;
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedExportTemplateDeactivated';
                }
                else
                {
                    $message = 'SelectedExportTemplatesDeactivated';
                }
                $tab = BrowserComponent :: TAB_EXPORT_TEMPLATES;
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
            $this->display_error_page(htmlentities(Translation :: get('SelectedPublicationExportTemplatesSelected')));
        }
    }
}
?>