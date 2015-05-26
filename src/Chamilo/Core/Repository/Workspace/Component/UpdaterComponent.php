<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Form\WorkspaceForm;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $workspaceId = Request :: get(self :: PARAM_WORKSPACE_ID);
        $workspace = DataManager :: retrieve_by_id(Workspace :: class_name(), $workspaceId);

        $form = new WorkspaceForm($this->get_url(array(self :: PARAM_WORKSPACE_ID => $workspace->getId())), $workspace);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                $values[Workspace :: PROPERTY_CREATOR_ID] = $workspace->getCreatorId();
                $values[Workspace :: PROPERTY_CREATION_DATE] = $workspace->getCreationDate();

                $workspaceService = new WorkspaceService(new WorkspaceRepository());
                $success = $workspaceService->updateWorkspace($workspace, $values);

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('Workspace')),
                    Utilities :: COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Adds additional breadcrumbs
     *
     * @param \libraries\format\BreadcrumbTrail $breadcrumb_trail
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE)),
                Translation :: get('BrowserComponent')));
    }
}