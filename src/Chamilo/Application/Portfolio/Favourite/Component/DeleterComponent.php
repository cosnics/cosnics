<?php
namespace Chamilo\Application\Portfolio\Favourite\Component;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * Deletes the given favourites
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DeleterComponent extends Manager
{

    /**
     * Executes this component
     */
    function run()
    {
        $favouriteService = $this->getFavouriteService();
        $userFavouriteIds = $this->getRequest()->get(self::PARAM_FAVOURITE_ID);

        try
        {
            $favouriteService->deleteUserFavouritesById($userFavouriteIds);

            $objectTranslation = $this->getTranslator()->trans('UserFavourite', [], Manager::context());

            $success = true;
            $message = $this->getTranslator()->trans(
                'ObjectDeleted',
                array('OBJECT' => $objectTranslation),
                Utilities::COMMON_LIBRARIES);
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $source = $this->getRequest()->get(self::PARAM_SOURCE);
        if (! $source || $source == self::SOURCE_FAVOURITES_BROWSER)
        {
            $returnParameters = array(self::PARAM_ACTION => self::ACTION_BROWSE);
            $filterParameters = [];
        }
        else
        {
            $returnParameters = array(
                \Chamilo\Application\Portfolio\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE);

            $filterParameters = array(self::PARAM_ACTION);
        }

        $this->redirect($message, ! $success, $returnParameters, $filterParameters);
    }
}