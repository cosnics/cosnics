<?php
namespace Chamilo\Application\Portfolio\Favourite\Component;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Creates a new favourite
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreatorComponent extends Manager
{

    /**
     * Executes this component
     */
    function run()
    {
        $favouriteService = $this->getFavouriteService();
        $favouriteUserIds = $this->getRequest()->get(self::PARAM_FAVOURITE_USER_ID);

        try
        {
            $favouriteService->createUserFavouritesByUserIds($this->getUser(), $favouriteUserIds);

            $objectTranslation = $this->getTranslator()->trans('UserFavourite', [], Manager::context());

            $success = true;
            $message = $this->getTranslator()->trans(
                'ObjectCreated',
                array('OBJECT' => $objectTranslation),
                Utilities::COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $this->redirect(
            $message,
            ! $success,
            array(
                \Chamilo\Application\Portfolio\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE),
            array(self::PARAM_ACTION));
    }
}