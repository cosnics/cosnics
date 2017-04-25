<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;

/**
 * Base component for reporting
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class BaseReportingComponent extends BaseHtmlTreeComponent
{
    /**
     * Renders common functionality
     */
    function renderCommonFunctionality()
    {
        $translator = Translation::getInstance();

        $html = array();

        if ($this->getUser() !== $this->getReportingUser())
        {
            $html[] = $this->renderViewAsMessage($translator, $this->getReportingUser());
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders a message when viewing this component as another user
     *
     * @param Translation $translator
     * @param User $user
     *
     * @return string
     */
    protected function renderViewAsMessage(Translation $translator, User $user)
    {
        $userListUrl = $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_VIEW_USER_PROGRESS), array(self::PARAM_REPORTING_USER_ID)
        );

        return '<div class="alert alert-info"><div class="pull-left" style="margin-top: 6px;">' .
            $translator->getTranslation('ViewReportingAsUser', array('USER' => $user->get_fullname())) . '</div>' .
            ' <a class="btn btn-sm btn-default pull-right" href="' . $userListUrl . '">' .
            $translator->getTranslation('ReturnToUserList') . '</a><div class="clearfix"></div></div>';
    }

    /**
     * Returns and validates the user that can be used in the reporting, makes sure that the current user
     * is allowed to view the reporting as the given user
     *
     * @return User|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getReportingUser()
    {
        if (!$this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            return $this->getUser();
        }

        $userId = $this->getRequest()->get(self::PARAM_REPORTING_USER_ID);
        if (empty($userId))
        {
            return $this->getUser();
        }

        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class_name(), $userId);
        if (!$user instanceof User)
        {
            return $this->getUser();
        }

        return $user;
    }

    /**
     * @return array
     */
    public function get_additional_parameters()
    {
        $additionalParameters = parent::get_additional_parameters();
        $additionalParameters[] = self::PARAM_REPORTING_USER_ID;

        return $additionalParameters;
    }

}