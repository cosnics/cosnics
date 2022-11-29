<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Service\GradeBookService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_ACTION = 'gradebook_display_action';
    const PARAM_USER_ID = 'user_id';

    const DEFAULT_ACTION = 'Browser';

    const ACTION_AJAX = 'Ajax';
    const ACTION_EXPORT = 'Export';
    const ACTION_BROWSE = 'Browser';
    const ACTION_IMPORT_CSV = 'ImportCSV'; 
    const ACTION_USER_SCORES = 'UserScores';

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
            $this->rightsService->setGradeBookServiceBridge($this->getGradeBookServiceBridge());
        }

        return $this->rightsService;
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
            throw new UserException($this->getTranslator()->trans('CanNotViewGradeBook', [], Manager::context()));
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
     * @return GradeBookService
     */
    protected function getGradeBookService()
    {
        return $this->getService(GradeBookService::class);
    }

    /**
     * @return GradeBookServiceBridgeInterface
     */
    protected function getGradeBookServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(GradeBookServiceBridgeInterface::class);
    }

    /**
     * @return GradeBook
     * @throws UserException
     */
    public function getGradeBook(): GradeBook
    {
        $gradebook = $this->get_root_content_object();

        if (!$gradebook instanceof GradeBook)
        {
            $this->throwUserException('GradeBookNotFound');
        }

        return $gradebook;
    }

    /**
     * @throws NotAllowedException
     * @throws UserException
     */
    public function validateGradeBookUserInput()
    {
        $this->validateIsPostRequest();
        $this->validateIsGradeBook();
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
    protected function validateIsGradeBook()
    {
        $gradebook = $this->get_root_content_object();

        if (!$gradebook instanceof GradeBook)
        {
            $this->throwUserException('GradeBookNotFound');
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
}
