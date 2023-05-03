<?php
namespace Chamilo\Application\Portfolio;

use Chamilo\Application\Portfolio\Favourite\Service\FavouriteService;
use Chamilo\Application\Portfolio\Service\FeedbackService;
use Chamilo\Application\Portfolio\Service\NotificationService;
use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\RightsService as WorkspaceRightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Application\Portfolio
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_BROWSE_FAVOURITES = 'Favourites';
    public const ACTION_HOME = 'Home';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_HOME;

    public const PARAM_USER_ID = 'user_id';

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }

    /**
     * Get the "current" user object, which is either the user of whom we are viewing the portfolio or the currently
     * logged-in user
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getCurrentUser()
    {
        return $this->getUserService()->findUserByIdentifier($this->getCurrentUserId());
    }

    /**
     * Get the "current" user id, which is either the user of whom we are viewing the portfolio or the currently
     * logged-in user
     *
     * @return int
     */
    public function getCurrentUserId()
    {
        return $this->getRequest()->query->get(self::PARAM_USER_ID, $this->getUser()->getId());
    }

    /**
     * @return \Chamilo\Application\Portfolio\Favourite\Service\FavouriteService
     */
    public function getFavouriteService()
    {
        return $this->getService(FavouriteService::class);
    }

    /**
     * @return \Chamilo\Application\Portfolio\Service\FeedbackService
     */
    public function getFeedbackService()
    {
        return $this->getService(FeedbackService::class);
    }

    /**
     * @return \Chamilo\Application\Portfolio\Service\NotificationService
     */
    public function getNotificationService()
    {
        return $this->getService(NotificationService::class);
    }

    /**
     * @return \Chamilo\Application\Portfolio\Service\PublicationService
     */
    public function getPublicationService()
    {
        return $this->getService(PublicationService::class);
    }

    /**
     * @return \Chamilo\Application\Portfolio\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->getService(RightsService::class);
    }

    /**
     * @return \Chamilo\Core\Repository\Workspace\Service\RightsService
     */
    public function getWorkspaceRightsService()
    {
        return $this->getService(WorkspaceRightsService::class);
    }
}