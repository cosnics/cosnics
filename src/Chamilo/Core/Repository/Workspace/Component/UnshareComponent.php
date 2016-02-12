<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

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

    public function run()
    {
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

        $this->redirect(
            Translation :: get('ContentObjectsUnshared'),
            false,
            array(
                self :: PARAM_ACTION => null,
                \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_BROWSE_CONTENT_OBJECTS));
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
        return $this->get_application()->getWorkspace();
    }
}
