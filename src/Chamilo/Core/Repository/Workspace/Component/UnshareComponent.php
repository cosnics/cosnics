<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UnshareComponent extends Manager
{

    /**
     * @var Workspace
     */
    protected $selectedWorkspace;

    /**
     * @var int
     */
    private $selectedContentObjectIdentifiers;

    public function run()
    {
        $canDelete = $this->getRightsService()->canDeleteContentObjects($this->getUser(), $this->getCurrentWorkspace());

        if (!$canDelete)
        {
            throw new NotAllowedException();
        }

        $selectedContentObjectIdentifiers = $this->getSelectedContentObjectIdentifiers();

        if (empty($selectedContentObjectIdentifiers))
        {
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        foreach ($selectedContentObjectIdentifiers as $selectedContentObjectIdentifier)
        {
            $contentObject = DataManager::retrieve_by_id(ContentObject::class, $selectedContentObjectIdentifier);

            $this->getContentObjectRelationService()->deleteContentObjectRelationByWorkspaceAndContentObject(
                $this->getCurrentWorkspace(), $contentObject
            );
        }

        $source = Request::get(self::PARAM_BROWSER_SOURCE);
        $returnComponent = isset($source) ? $source : \Chamilo\Core\Repository\Manager::ACTION_BROWSE_CONTENT_OBJECTS;

        $this->redirectWithMessage(
            Translation::get('ContentObjectsUnshared'), false,
            [self::PARAM_ACTION => null, \Chamilo\Core\Repository\Manager::PARAM_ACTION => $returnComponent]
        );
    }

    /**
     * @see \Chamilo\Core\Repository\Manager::getAdditionalParameters()
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }

    protected function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->getService(ContentObjectRelationService::class);
    }

    public function getCurrentWorkspace()
    {
        $selectedWorkspace = $this->getSelectedWorkspace();
        if ($selectedWorkspace instanceof Workspace)
        {
            return $selectedWorkspace;
        }

        return $this->get_application()->getCurrentWorkspace();
    }

    /**
     * @return int
     */
    public function getSelectedContentObjectIdentifiers()
    {
        if (!isset($this->selectedContentObjectIdentifiers))
        {
            $this->selectedContentObjectIdentifiers = (array) $this->getRequest()->get(
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID, []
            );
        }

        return $this->selectedContentObjectIdentifiers;
    }

    public function getSelectedWorkspace(): ?Workspace
    {
        if (!isset($selectedWorkspace))
        {
            $workspaceIdentifier = $this->getRequest()->query->get(self::PARAM_SELECTED_WORKSPACE_ID);

            if (isset($workspaceIdentifier))
            {
                $this->selectedWorkspace = $this->getWorkspaceService()->determineWorkspaceForUserByIdentifier(
                    $this->getUser(), $workspaceIdentifier
                );
            }
        }

        return $this->selectedWorkspace;
    }
}
