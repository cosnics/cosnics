<?php
namespace Chamilo\Application\Survey\Rights;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Survey
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ENTITY_RELATION_ID = 'entity_relation_id';
    const PARAM_ACTION = 'rights_action';
    
    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_CREATE = 'Creator';
    const ACTION_UPDATE = 'Updater';
    const ACTION_RIGHTS = 'Rights';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_CREATE;

    /**
     *
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function getCurrentPublication()
    {
        $publicationService = new PublicationService(new PublicationRepository());
        $publicationId = $this->getCurrentPublicationIdentifier();
        if ($publicationId)
        {
            return $publicationService->getPublicationByIdentifier($this->getCurrentPublicationIdentifier());
        }
        else
        {
            $publication = new Publication();
            $publication->setId(0);
            $publication->setPublisherId(0);
            return $publication;
        }
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\mixed
     */
    public function getCurrentPublicationIdentifier()
    {
        return $this->getRequest()->query->get(\Chamilo\Application\Survey\Manager::PARAM_PUBLICATION_ID);
    }

    protected function determineRightType()
    {
        $publicationId = $this->getCurrentPublicationIdentifier();
        
        if ($publicationId)
        {
            return RightsService::PUBLICATION_RIGHTS;
        }
        else
        {
            return RightsService::APPLICATION_RIGHTS;
        }
    }
}
