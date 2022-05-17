<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

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

        $html = [];

        $buttonToolbarRenderer = $this->getButtonToolbarRenderer($translator);
        $html[] = $buttonToolbarRenderer->render();

        if ($this->getUser() !== $this->getReportingUser())
        {
            $html[] = $this->renderViewAsMessage($translator, $this->getReportingUser());
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param Translation $translator
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer(Translation $translator)
    {
        return new ButtonToolBarRenderer($this->getButtonToolbar($translator));
    }

    /**
     * Builds and returns the button toolbar
     *
     * @param Translation $translator
     *
     * @return ButtonToolBar
     */
    protected function getButtonToolbar(Translation $translator)
    {
        $buttonToolbar = new ButtonToolBar();

        $buttonToolbar->addItem(
            new Button(
                $translator->getTranslation('ReturnToLearningPath'),
                new FontAwesomeGlyph('file'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT))
            )
        );

        return $buttonToolbar;
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
        $returnToUserListUrl = $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_VIEW_USER_PROGRESS), array(self::PARAM_REPORTING_USER_ID)
        );

        $returnToUserListTranslation = $translator->getTranslation('ReturnToUserList');

        $html = [];

        $html[] = '<div class="alert alert-info">';
        $html[] = '<div class="pull-left" style="margin-top: 6px;">';
        $html[] = $translator->getTranslation('ViewReportingAsUser', array('USER' => $user->get_fullname()));
        $html[] = '</div>';
        $html[] = '<div class="pull-right">';
        $html[] = '<a href="' . $returnToUserListUrl . '" class="btn btn-default">';
        $html[] = '<span class="inline-glyph fas fa-chart-bar"></span>';
        $html[] = $returnToUserListTranslation;
        $html[] = '</a>';
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns and validates the user that can be used in the reporting, makes sure that the current user
     * is allowed to view the reporting as the given user
     *
     * @return User|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getReportingUser()
    {
        if (!$this->canEditCurrentTreeNode())
        {
            return $this->getUser();
        }

        $userId = $this->getRequest()->get(self::PARAM_REPORTING_USER_ID);
        if (empty($userId))
        {
            return $this->getUser();
        }

        $user = DataManager::retrieve_by_id(User::class, $userId);
        if (!$user instanceof User)
        {
            return $this->getUser();
        }

        return $user;
    }

    /**
     * @return array
     */
    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_REPORTING_USER_ID;

        return parent::get_additional_parameters($additionalParameters);
    }

    /**
     * Returns the user that is used to calculate and render the progress in the tree
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    protected function getTreeUser()
    {
        return $this->getReportingUser();
    }

}