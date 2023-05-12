<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Form\WorkspaceForm;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends TabComponent
{

    /**
     * Adds additional breadcrumbs
     *
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $browserSource = $this->get_parameter(self::PARAM_BROWSER_SOURCE);

        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url([Manager::PARAM_ACTION => $browserSource]),
                Translation::get($browserSource . 'Component')
            )
        );
    }

    /**
     * Executes this controller
     */
    public function build()
    {
        $workspaceId = Request::get(self::PARAM_WORKSPACE_ID);

        /** @var Workspace $workspace */
        $workspace = DataManager::retrieve_by_id(Workspace::class, $workspaceId);

        $form = new WorkspaceForm($this->get_url([self::PARAM_WORKSPACE_ID => $workspace->getId()]), $workspace);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                $values[Workspace::PROPERTY_CREATOR_ID] = $workspace->getCreatorId();
                $values[Workspace::PROPERTY_CREATION_DATE] = $workspace->getCreationDate();

                $success = $this->getWorkspaceService()->updateWorkspace($workspace, $values);

                if ($success)
                {
                    $this->getWorkspaceExtensionManager()->workspaceUpdated($workspace, $this->getUser());
                }

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation::get(
                    $translation, ['OBJECT' => Translation::get('Workspace')], StringUtilities::LIBRARIES
                );
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $source = $this->getRequest()->getFromRequestOrQuery(self::PARAM_BROWSER_SOURCE);
            $returnComponent = isset($source) ? $source : self::ACTION_BROWSE;

            $this->redirectWithMessage($message, !$success, [self::PARAM_ACTION => $returnComponent]);
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

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_WORKSPACE_ID;
        $additionalParameters[] = self::PARAM_BROWSER_SOURCE;

        return parent::getAdditionalParameters($additionalParameters);
    }
}