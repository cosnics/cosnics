<?php
namespace Chamilo\Application\Portfolio\Favourite\Component;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

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
        $favouriteUserIds = $this->getRequest()->getFromRequestOrQuery(self::PARAM_FAVOURITE_USER_ID);

        try
        {
            $favouriteService->createUserFavouritesByUserIds($this->getUser(), $favouriteUserIds);

            $objectTranslation = $this->getTranslator()->trans('UserFavourite', [], Manager::CONTEXT);

            $success = true;
            $message = $this->getTranslator()->trans(
                'ObjectCreated',
                array('OBJECT' => $objectTranslation),
                StringUtilities::LIBRARIES);
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $this->redirectWithMessage(
            $message,
            ! $success,
            array(
                \Chamilo\Application\Portfolio\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE),
            array(self::PARAM_ACTION));
    }
}