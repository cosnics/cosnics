<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Form\WorkspaceForm;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        $form = new WorkspaceForm($this->get_url());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                $values[Workspace :: PROPERTY_CREATOR_ID] = $this->get_user_id();
                $values[Workspace :: PROPERTY_CREATION_DATE] = time();

                $workspaceService = new WorkspaceService(new WorkspaceRepository());
                $success = $workspaceService->createWorkspace($values);

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

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

            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_RIGHTS));
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