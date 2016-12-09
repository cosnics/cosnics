<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * $Id: subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * 
 * @package application.lib.weblcms.weblcms_manager.component
 */

/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class SubscribeComponent extends Manager
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED_NOT_ALLOWED = 2;
    const STATUS_FAILED_REQUEST = 3;
    const STATUS_FAILED_UNKNOWN = 4;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }
        
        $course_id = $this->get_course_id();
        $userIds = $this->getRequest()->get(self::PARAM_OBJECTS);
        if (isset($userIds) && ! is_array($userIds))
        {
            $userIds = array($userIds);
        }
        
        if (isset($course_id))
        {
            if (isset($userIds) && count($userIds) > 0)
            {
                $failures = 0;
                $usersStatus = array();
                
                $course_management_rights = CourseManagementRights::getInstance();
                
                $users = $this->findUsersByIds($userIds);
                
                foreach ($users as $user)
                {
                    $userId = $user->getId();
                    
                    $status = Request::get(self::PARAM_STATUS) ? Request::get(self::PARAM_STATUS) : 5;
                    
                    if (\Chamilo\Application\Weblcms\Course\Storage\DataManager::is_user_direct_subscribed_to_course(
                        $userId, 
                        $course_id) || (! $this->get_user()->is_platform_admin() && ! $course_management_rights->is_allowed(
                        CourseManagementRights::TEACHER_DIRECT_SUBSCRIBE_RIGHT, 
                        $course_id, 
                        CourseManagementRights::TYPE_COURSE, 
                        $userId)))
                    {
                        $requestRight = $course_management_rights->is_allowed(
                            CourseManagementRights::TEACHER_REQUEST_SUBSCRIBE_RIGHT, 
                            $course_id, 
                            CourseManagementRights::TYPE_COURSE, 
                            $userId);
                        
                        $failures ++;
                        
                        if ($requestRight)
                        {
                            $usersStatus[self::STATUS_FAILED_REQUEST][] = $user;
                        }
                        else
                        {
                            $usersStatus[self::STATUS_FAILED_NOT_ALLOWED][] = $user;
                        }
                        
                        continue;
                    }
                    
                    if (! \Chamilo\Application\Weblcms\Course\Storage\DataManager::subscribe_user_to_course(
                        $course_id, 
                        $status, 
                        $userId))
                    {
                        $failures ++;
                        $usersStatus[self::STATUS_FAILED_UNKNOWN][] = $user;
                        continue;
                    }
                    
                    $usersStatus[self::STATUS_SUCCESS][] = $user;
                }
                
                if ($failures == 0)
                {
                    if (count($users) == 1)
                    {
                        $message = 'UserSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'UsersSubscribedToCourse';
                    }
                    
                    $this->redirect(
                        Translation::get($message), 
                        false, 
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER));
                }
                else
                {
                    return $this->renderStatusPage($usersStatus);
                }
            }
            else
            {
                throw new NoObjectSelectedException(
                    Translation::getInstance()->getTranslation('Users', null, Manager::context()));
            }
        }
        else
        {
            
            throw new NoObjectSelectedException(
                Translation::getInstance()->getTranslation(
                    'Course', 
                    null, 
                    \Chamilo\Application\Weblcms\Manager::context()));
        }
    }

    /**
     * Renders a detailed status page when there are users that could not be subscribed
     * 
     * @param user[][] $usersStatus
     *
     * @return string
     */
    protected function renderStatusPage($usersStatus = array())
    {
        $translator = Translation::getInstance();
        
        $html = array();
        
        $html[] = $this->render_header();
        
        $html[] = $this->renderUserList(
            'panel-info', 
            $translator->getTranslation('SubscribedUsers', null, Manager::context()), 
            $usersStatus[self::STATUS_SUCCESS]);
        
        $html[] = $this->renderUserList(
            'panel-danger', 
            $translator->getTranslation('FailedUsersNotAllowed', null, Manager::context()), 
            $usersStatus[self::STATUS_FAILED_NOT_ALLOWED]);
        
        $html[] = $this->renderRequestUserList($translator, $usersStatus[self::STATUS_FAILED_REQUEST]);
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the user list for users that must be requested
     * 
     * @param Translation $translator
     * @param User[] $requestUsers
     *
     * @return string
     */
    protected function renderRequestUserList(Translation $translator, array $requestUsers = array())
    {
        $requestUserIds = array();
        
        foreach ($requestUsers as $requestUser)
        {
            $requestUserIds[] = $requestUser->getId();
        }
        
        $requestUrl = $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_REQUEST_SUBSCRIBE_USERS, self::PARAM_OBJECTS => $requestUserIds));
        
        $additionalItems = array(
            '<a href="' . $requestUrl . '">' . '<button type="button" class="btn btn-default pull-right">' .
                 $translator->getTranslation('RequestUsers', null, Manager::context()) .
                 '</button><div class="clearfix"></div></a>');
        
        return $this->renderUserList(
            'panel-warning', 
            $translator->getTranslation('FailedUsersCanOnlyBeRequested', null, Manager::context()), 
            $requestUsers, 
            $additionalItems);
    }

    /**
     * Finds user objects by a given array of user ids
     * 
     * @param int[] $userIds
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    protected function findUsersByIds(array $userIds = array())
    {
        $userRepository = new UserRepository();
        
        return $userRepository->findUsers(
            new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $userIds));
    }

    /**
     * Renders a status user list
     * 
     * @param string $statusClass
     * @param string $statusTitle
     * @param User[] $users
     * @param array $additionalItems
     *
     * @return string
     */
    protected function renderUserList($statusClass, $statusTitle, $users = array(), array $additionalItems = array())
    {
        if (empty($users))
        {
            return '';
        }
        
        $html = array();
        
        $html[] = '<div class="panel ' . $statusClass . '">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = $statusTitle;
        $html[] = '</h3>';
        $html[] = '</div>';
        
        $html[] = '<ul class="list-group">';
        foreach ($users as $failedUser)
        {
            $html[] = '<li class="list-group-item">';
            $html[] = '<h5 class="list-group-item-heading">';
            $html[] = $failedUser->get_fullname();
            $html[] = '</h5>';
            $html[] = '<p class="list-group-item-text">';
            $html[] = $failedUser->get_email();
            $html[] = '</p>';
            $html[] = '</li>';
        }
        
        foreach ($additionalItems as $additionalItem)
        {
            $html[] = '<li class="list-group-item">';
            $html[] = $additionalItem;
            $html[] = '</li>';
        }
        
        $html[] = '</ul>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
