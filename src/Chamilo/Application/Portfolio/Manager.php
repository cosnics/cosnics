<?php
namespace Chamilo\Application\Portfolio;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * Portfolio Application
 *
 * @package application\portfolio$Manager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_USER_ID = 'user_id';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_BROWSE_FAVOURITES = 'Favourites';
    const ACTION_HOME = 'Home';

    // Default action
    const DEFAULT_ACTION = self::ACTION_HOME;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
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
     *
     * @return \Chamilo\Application\Portfolio\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->getService('chamilo.application.portfolio.service.rights_service');
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\RightsService
     */
    public function getWorkspaceRightsService()
    {
        return \Chamilo\Core\Repository\Workspace\Service\RightsService::getInstance();
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Service\FeedbackService
     */
    public function getFeedbackService()
    {
        return $this->getService('chamilo.application.portfolio.service.feedback_service');
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Service\NotificationService
     */
    public function getNotificationService()
    {
        return $this->getService('chamilo.application.portfolio.service.notification_service');
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Service\PublicationService
     */
    public function getPublicationService()
    {
        return $this->getService('chamilo.application.portfolio.service.publication_service');
    }
}