<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display;

use Chamilo\Application\Presence\Service\PresenceRegistrationService;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\ExportService;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\QRService;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\RightsService;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

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
    const ACTION_EXPORT = 'Export';
    const ACTION_PRINT_QR_CODE = 'PrintPresenceRegistrationQr';

    const ACTION_USER_PRESENCES = 'UserPresences';

    /**
     *
     * @var integer
     */
    protected $userIdentifier;

    /**
     * @var RightsService
     */
    protected $rightsService;

    protected function ensureUserIdentifier()
    {
        $userIdentifier = $this->getUserIdentifier();
        if ($userIdentifier)
        {
            $this->set_parameter(self::PARAM_USER_ID, $userIdentifier);
        }
    }

    /**
     * @return RightsService
     */
    public function getRightsService()
    {
        if (!isset($this->rightsService))
        {
            $this->rightsService = new RightsService();
            $this->rightsService->setPresenceServiceBridge($this->getPresenceServiceBridge());
        }

        return $this->rightsService;
    }

    /**
     * @return ExportService
     */
    protected function getExportService(): ExportService
    {
        return $this->getService(ExportService::class);
    }

    /**
     * @return QRService
     */
    protected function getQRService(): QRService
    {
        return $this->getService(QRService::class);
    }

    /**
     * @return mixed
     */
    protected function getUserIdentifier()
    {
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
     * @return ContentObjectService
     */
    protected function getContentObjectService()
    {
        return $this->getService(ContentObjectService::class);
    }

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

        if (!$presence instanceof Presence)
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
        /*$userIds = $this->getPresenceServiceBridge()->getTargetUserIds();

        if (! in_array($userId, $userIds))
        {
            $this->throwUserException('UserNotInList');
        }*/
    }

    /**
     * @param string $key
     *
     * @throws UserException
     */
    public function throwUserException(string $key = '')
    {
        throw new UserException(
            $this->getTranslator()->trans($key, [], Manager::context())
        );
    }

    /**
     * @param bool $qr
     *
     * @return string
     * @throws UserException
     */
    protected function getRegisterPresenceUrl(bool $qr = false): string
    {
        $url = $this->getPresenceRegistrationService()->getPresenceRegistrationUrl(
            $this->getPresence(), $this->getPublicationId(), $this->getTreeNodeId(), $this->getPresencePeriodId()
        );

        if ($qr)
        {
            return $this->getQRService()->getQRForURL($url, 500, true);
        }

        return $url;
    }

    /**
     * @return PresenceRegistrationService
     */
    protected function getPresenceRegistrationService()
    {
        return $this->getService(PresenceRegistrationService::class);
    }

    /**
     * @return int|null
     */
    protected function getPublicationId(): ?int
    {
        return $this->getRequest()->getFromUrl(
            \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION
        );
    }

    /**
     * @return int|null
     */
    protected function getTreeNodeId(): ?int
    {
        return $this->getRequest()->getFromUrl(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID
        );
    }

    /**
     * @return int|null
     */
    protected function getPresencePeriodId(): ?int
    {
        return $this->getRequest()->getFromUrl(\Chamilo\Application\Presence\Manager::PARAM_PRESENCE_PERIOD_ID);
    }
}
