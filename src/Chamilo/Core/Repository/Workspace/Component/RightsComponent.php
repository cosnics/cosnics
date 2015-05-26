<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Workspace\Form\RightsForm;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsComponent extends Manager
{

    public function run()
    {
        $workspaceId = Request :: get(self :: PARAM_WORKSPACE_ID);
        $workspace = DataManager :: retrieve_by_id(Workspace :: class_name(), $workspaceId);

        $form = new RightsForm($this->get_url(array(self :: PARAM_WORKSPACE_ID => $workspace->getId())), $workspace);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $entityRelationService = new EntityRelationService(new EntityRelationRepository());
                $success = $entityRelationService->setEntityRelations(
                    $workspace,
                    $values[RightsForm :: PROPERTY_ACCESS],
                    (int) $values[RightsForm :: PROPERTY_VIEW],
                    (int) $values[RightsForm :: PROPERTY_USE],
                    (int) $values[RightsForm :: PROPERTY_COPY]);

                $translation = $success ? 'RightsSet' : 'RightsNotSet';
                $message = Translation :: get($translation);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect(
                $message,
                ! $success,
                array(self :: PARAM_ACTION => self :: ACTION_RIGHTS, self :: PARAM_WORKSPACE_ID => $workspace->getId()));
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
}