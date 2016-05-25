<?php
namespace Chamilo\Application\Survey\Rights\Component;

use Chamilo\Application\Survey\Repository\EntityRelationRepository;
use Chamilo\Application\Survey\Rights\Manager;
use Chamilo\Application\Survey\Service\EntityRelationService;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Survey\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $entityRelationIdentifiers = $this->getRequest()->get(self :: PARAM_ENTITY_RELATION_ID);

        try
        {
            if (empty($entityRelationIdentifiers))
            {
                throw new NoObjectSelectedException(Translation :: get('PublicationEntityRelation'));
            }

            if (! is_array($entityRelationIdentifiers))
            {
                $entityRelationIdentifiers = array($entityRelationIdentifiers);
            }

            $entityRelationService = new EntityRelationService(new EntityRelationRepository());

            $rightsService = RightsService :: getInstance();

            foreach ($entityRelationIdentifiers as $entityRelationIdentifier)
            {
                $entityRelation = $entityRelationService->getEntityRelationByIdentifier($entityRelationIdentifier);

                if ($rightsService->hasPublicationCreatorRights($this->get_user(), $this->getCurrentPublication()))
                {
                    if (! $entityRelationService->deleteEntityRelation($entityRelation))
                    {
                        throw new \Exception(
                            Translation :: get(
                                'ObjectNotDeleted',
                                array('OBJECT' => Translation :: get('Publication')),
                                Utilities :: COMMON_LIBRARIES));
                    }
                }
            }

            $success = true;
            $message = Translation :: get(
                'ObjectDeleted',
                array('OBJECT' => Translation :: get('Publication')),
                Utilities :: COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }
}