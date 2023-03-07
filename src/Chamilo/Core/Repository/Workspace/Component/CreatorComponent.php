<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Form\WorkspaceForm;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends TabComponent
{

    public function build()
    {
        $form = new WorkspaceForm($this->get_url());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                $values[Workspace::PROPERTY_CREATOR_ID] = $this->get_user_id();
                $values[Workspace::PROPERTY_CREATION_DATE] = time();

                $workspace = $this->getWorkspaceService()->createWorkspace($values);

                $success = $workspace instanceof Workspace;
                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

                $message = Translation::get(
                    $translation, ['OBJECT' => Translation::get('Workspace')], StringUtilities::LIBRARIES
                );

                if (!$success)
                {
                    throw new Exception($message);
                }

                $redirectParameters = [
                    self::PARAM_ACTION => self::ACTION_RIGHTS,
                    self::PARAM_WORKSPACE_ID => $workspace->getId()
                ];
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();

                $redirectParameters = [self::PARAM_ACTION => self::ACTION_BROWSE_PERSONAL];
            }

            $this->redirectWithMessage($message, !$success, $redirectParameters);
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}