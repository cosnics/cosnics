<?php
namespace Chamilo\Application\Survey\Table\Share;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ShareTableDataProvider extends DataClassTableDataProvider
{

    /**
     *
     * @var \Chamilo\Application\Survey\Service\PublicationService
     */
    private $publicationService;

    /**
     *
     * @see \Chamilo\Application\Survey\Table\Publication\PublicationTableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        return $this->getPublicationService()->getPublicationsForUser(
            $this->get_component()->get_user(), 
            RightsService::RIGHT_ADD, 
            $limit, 
            $offset, 
            $orderProperty);
    }

    /**
     *
     * @see \Chamilo\Application\Survey\Table\Publication\PublicationTableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        return $this->getPublicationService()->countPublicationsForUser(
            $this->get_component()->get_user(), 
            RightsService::RIGHT_ADD);
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Service\PublicationService
     */
    private function getPublicationService()
    {
        if (! isset($this->publicationService))
        {
            $this->publicationService = new PublicationService(new PublicationRepository());
        }
        
        return $this->publicationService;
    }
}