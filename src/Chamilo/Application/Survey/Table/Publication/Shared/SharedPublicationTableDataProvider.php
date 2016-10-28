<?php
namespace Chamilo\Application\Survey\Table\Publication\Shared;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Application\Survey\Table\Publication\PublicationTableDataProvider;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication\Shared
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SharedPublicationTableDataProvider extends PublicationTableDataProvider
{

    /**
     *
     * @see \Chamilo\Application\Survey\Table\Publication\PublicationTableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        $publicationService = new PublicationService(new PublicationRepository());
        return $publicationService->getSharedPublicationsForUser(
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
        $publicationService = new PublicationService(new PublicationRepository());
        return $publicationService->countSharedPublicationsForUser($this->get_component()->get_user());
    }
}