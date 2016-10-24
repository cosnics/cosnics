<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\Repository\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UnshareComponent extends Manager
{

    /**
     *
     * @var integer[]
     */
    private $selectedContentObjectIdentifiers;

    /**
     * @var Workspace
     */
    protected $selectedWorkspace;

    public function run()
    {
        $rightsService = RightsService::getInstance();
        $canDelete = $rightsService->canDeleteContentObjects(
            $this->getUser(),
            $this->getCurrentWorkspace()
        );

        if(!$canDelete)
        {
            throw new NotAllowedException();
        }

        $selectedContentObjectIdentifiers = $this->getSelectedContentObjectIdentifiers();

        if (empty($selectedContentObjectIdentifiers))
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObject'));
        }

        $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());

        foreach ($selectedContentObjectIdentifiers as $selectedContentObjectIdentifier)
        {
            $contentObject = DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $selectedContentObjectIdentifier);

            $contentObjectRelationService->deleteContentObjectRelationByWorkspaceAndContentObjectIdentifier(
                $this->getCurrentWorkspace(),
                $contentObject);
        }

        $source = Request::get(self::PARAM_BROWSER_SOURCE);
        $returnComponent = isset($source) ? $source :  \Chamilo\Core\Repository\Manager::ACTION_BROWSE_CONTENT_OBJECTS;

        $this->redirect(
            Translation :: get('ContentObjectsUnshared'),
            false,
            array(
                self :: PARAM_ACTION => null, \Chamilo\Core\Repository\Manager :: PARAM_ACTION => $returnComponent
            )
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Manager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @return integer[]
     */
    public function getSelectedContentObjectIdentifiers()
    {
        if (! isset($this->selectedContentObjectIdentifiers))
        {
            $this->selectedContentObjectIdentifiers = (array) $this->getRequest()->get(
                \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID,
                array());
        }

        return $this->selectedContentObjectIdentifiers;
    }

    public function getCurrentWorkspace()
    {
        $selectedWorkspace = $this->getSelectedWorkspace();
        if($selectedWorkspace instanceof WorkspaceInterface)
        {
            return $selectedWorkspace;
        }

        return $this->get_application()->getWorkspace();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function getSelectedWorkspace()
    {
        if (! isset($selectedWorkspace))
        {
            $workspaceIdentifier = $this->getRequest()->query->get(self::PARAM_SELECTED_WORKSPACE_ID);
            if(isset($workspaceIdentifier))
            {
                $workspaceService = new WorkspaceService(new WorkspaceRepository());

                $this->selectedWorkspace = $workspaceService->determineWorkspaceForUserByIdentifier(
                    $this->getUser(),
                    $workspaceIdentifier
                );
            }
        }

        return $this->selectedWorkspace;
    }
}
