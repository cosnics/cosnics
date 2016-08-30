<?php
namespace Chamilo\Application\Survey\Table\Publication;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublicationTableDataProvider extends DataClassTableDataProvider
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        $publicationService = new PublicationService(new PublicationRepository());
        return $publicationService->getAllPublications($limit, $offset, $orderProperty);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        $publicationService = new PublicationService(new PublicationRepository());
        return $publicationService->countAllPublications();
    }
}