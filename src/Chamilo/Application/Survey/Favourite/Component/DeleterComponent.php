<?php
namespace Chamilo\Application\Survey\Favourite\Component;

use Chamilo\Application\Survey\Favourite\Manager;
use Chamilo\Application\Survey\Favourite\Repository\FavouriteRepository;
use Chamilo\Application\Survey\Favourite\Service\FavouriteService;
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
        $publicationIdentifiers = $this->getRequest()->query->get(
            \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID);

        try
        {
            if (empty($publicationIdentifiers))
            {
                throw new NoObjectSelectedException(Translation :: get('Publication'));
            }

            if (! is_array($publicationIdentifiers))
            {
                $publicationIdentifiers = array($publicationIdentifiers);
            }

            $favouriteService = new FavouriteService(new FavouriteRepository());

            foreach ($publicationIdentifiers as $publicationIdentifier)
            {
                
                $success = $favouriteService->deletePublicationByUserAndPublicationIdentifier(
                    $this->get_user(),
                    $publicationIdentifier);

                if (! $success)
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotDeleted',
                            array('OBJECT' => Translation :: get('PublicationUserFavourite')),
                            Utilities :: COMMON_LIBRARIES));
                }
            }

            $success = true;
            $message = Translation :: get(
                'ObjectDeleted',
                array('OBJECT' => Translation :: get('PublicationUserFavourite')),
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