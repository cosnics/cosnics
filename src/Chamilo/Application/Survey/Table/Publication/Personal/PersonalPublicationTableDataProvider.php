<?php
namespace Chamilo\Application\Survey\Table\Publication\Personal;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Application\Survey\Table\Publication\PublicationTableDataProvider;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PersonalPublicationTableDataProvider extends PublicationTableDataProvider
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
        return $this->getPublicationService()->getPublicationsByCreator(
            $this->get_component()->get_user(), 
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
        return $this->getPublicationService()->countPublicationsByCreator($this->get_component()->get_user());
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