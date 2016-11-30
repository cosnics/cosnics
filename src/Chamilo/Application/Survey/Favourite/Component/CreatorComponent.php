<?php
namespace Chamilo\Application\Survey\Favourite\Component;

use Chamilo\Application\Survey\Favourite\Manager;
use Chamilo\Application\Survey\Favourite\Repository\FavouriteRepository;
use Chamilo\Application\Survey\Favourite\Service\FavouriteService;
use Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite;
use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Survey\Rights\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        $publicationIdentifier = $this->getRequest()->get(\Chamilo\Application\Survey\Manager::PARAM_PUBLICATION_ID);
        
        if (! $publicationIdentifier)
        {
            throw new NoObjectSelectedException(Translation::get('Publication'));
        }
        
        $publicationService = new PublicationService(new PublicationRepository());
        $publication = $publicationService->getPublicationByIdentifier($publicationIdentifier);
        
        $favouriteService = new FavouriteService(new FavouriteRepository());
        $publicationUserFavourite = $favouriteService->createPublicationUserFavourite(
            $this->get_user(), 
            $publicationIdentifier);
        
        if ($publicationUserFavourite instanceof PublicationUserFavourite)
        {
            $this->redirect(
                Translation::get(
                    'ObjectCreated', 
                    array('OBJECT' => Translation::get('PublicationUserFavourite')), 
                    Utilities::COMMON_LIBRARIES), 
                false, 
                array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            $this->redirect(
                Translation::get(
                    'ObjectNotCreated', 
                    array('OBJECT' => Translation::get('PublicationUserFavourite')), 
                    Utilities::COMMON_LIBRARIES), 
                true, 
                array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
    }
}