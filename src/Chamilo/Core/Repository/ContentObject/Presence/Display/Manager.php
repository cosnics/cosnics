<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display;

/*use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceManager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\RightsService;*/

use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_ACTION = 'presence_display_action';
    const PARAM_USER_ID = 'user_id';

    const DEFAULT_ACTION = 'Browser';

    const ACTION_AJAX = 'Ajax';
    const ACTION_USER_PRESENCES = 'UserPresences';

    /**
     *
     * @var integer
     */
    protected $userIdentifier;

    /**
     * @var RightsService
     */
//    protected $rightsService;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->buildBridgeServices();
    }

/*    protected function ensureEntityIdentifier()
    {
        $entityIdentifier = $this->getEntityIdentifier();
        if ($entityIdentifier)
        {
            $this->set_parameter(self::PARAM_ENTITY_ID, $entityIdentifier);
        }
    }*/

    /**
     * @return RightsService
     */
/*    public function getRightsService()
    {
        if (!isset($this->rightsService))
        {
            $this->rightsService = new RightsService();
            $this->rightsService->setEvaluationServiceBridge($this->getEvaluationServiceBridge());
        }

        return $this->rightsService;
    }*/

    /**
     * @return mixed
     */
    protected function getUserIdentifier() {
        if (!isset($this->userIdentifier))
        {
            $this->userIdentifier = $this->getRequest()->getFromPostOrUrl(self::PARAM_USER_ID);

            if (empty($this->userIdentifier))
            {
                $this->userIdentifier = $this->getUser()->getId();
            }
        }

        if (empty($this->userIdentifier))
        {
            throw new UserException($this->getTranslator()->trans('CanNotViewPresence', [], Manager::context()));
        }

        return $this->userIdentifier;
    }

    /**
     * Builds the bridge services
     */
    protected function buildBridgeServices()
    {
    }

    /**
     * @return ContentObjectService
     */
    protected function getContentObjectService()
    {
        return $this->getService(ContentObjectService::class);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface
     */
/*    protected function getEvaluationServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(EvaluationServiceBridgeInterface::class);
    }*/

    /**
     * @return EvaluationEntityServiceInterface
     */
/*    protected function getEntityService(): EvaluationEntityServiceInterface
    {
        /** @var EvaluationEntityServiceManager $evaluationEntityServiceManager */
/*        $evaluationEntityServiceManager = $this->getService(EvaluationEntityServiceManager::class);
        return $evaluationEntityServiceManager->getEntityServiceByType($this->getEntityType());
    }*/

    /**
     * @return EvaluationEntryService
     */
/*    protected function getEvaluationEntryService()
    {
        return $this->getService(EvaluationEntryService::class);
    }*/

    /**
     * @return PresenceServiceBridgeInterface
     */
    protected function getPresenceServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(PresenceServiceBridgeInterface::class);
    }

    /**
     * @return Presence
     * @throws UserException
     */
    public function getPresence(): Presence
    {
        $presence = $this->get_root_content_object();

        if (!$presence instanceof Presence)
        {
            $this->throwUserException('PresenceNotFound');
        }

        return $presence;
    }

    /**
     * @throws NotAllowedException
     * @throws UserException
     */
    public function validatePresenceUserInput()
    {
        $this->validateIsPostRequest();
        $this->validateIsPresence();
        $this->validateUser();
    }

    /**
     * @throws NotAllowedException
     */
    public function validateIsPostRequest()
    {
        if (!$this->getRequest()->isMethod('POST'))
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @throws UserException
     */
    protected function validateIsPresence()
    {
        $presence = $this->get_root_content_object();

        if (! $presence instanceof Presence)
        {
            $this->throwUserException('PresenceNotFound');
        }
    }

    /**
     * @throws UserException
     */
    protected function validateUser()
    {
        $userId = $this->getUserIdentifier();

        if (empty($userId))
        {
            $this->throwUserException('UserIdNotProvided');
        }

        $userIds = $this->getPresenceServiceBridge()->getTargetUserIds();

        if (! in_array($userId, $userIds))
        {
            $this->throwUserException('UserNotInList');
        }
    }

    /**
     * @param string $key
     * @throws UserException
     */
    public function throwUserException($key = "")
    {
        throw new UserException(
            $this->getTranslator()->trans($key, [], Manager::context())
        );
    }

}