<?php
namespace Chamilo\Application\Survey\Favourite;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Survey\Favourite
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_FAVOURITE_ID = 'favourite_id';
    const PARAM_ACTION = 'favourite_action';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_CREATE = 'Creator';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     *
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function getCurrentPublication()
    {
        $publicationService = new PublicationService(new PublicationRepository());
        return $publicationService->getPublicationByIdentifier($this->getCurrentPublicationIdentifier());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\mixed
     */
    public function getCurrentPublicationIdentifier()
    {
        return $this->getRequest()->query->get(\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID);
    }
}
