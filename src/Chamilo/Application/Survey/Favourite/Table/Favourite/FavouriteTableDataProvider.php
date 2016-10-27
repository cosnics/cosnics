<?php
namespace Chamilo\Application\Survey\Favourite\Table\Favourite;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\EntityService;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Application\Survey\Table\Publication\PublicationTableDataProvider;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteTableDataProvider extends PublicationTableDataProvider
{

    /**
     *
     * @var \Chamilo\Application\Survey\Service\PublicationService
     */
    private $publicationService;

    /**
     *
     * @var \Chamilo\Application\Survey\Service\EntityService
     */
    private $entityService;

    /**
     *
     * @see \Chamilo\Application\Survey\Table\Publication\PublicationTableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        return $this->getPublicationService()->getPublicationFavouritesByUser(
            $this->getEntityService(),
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
        return $this->getPublicationService()->countPublicationFavouritesByUser(
            $this->getEntityService(),
            $this->get_component()->get_user());
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Service\EntityService
     */
    private function getEntityService()
    {
        if (! isset($this->entityService))
        {
            $this->entityService = new EntityService();
        }

        return $this->entityService;
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