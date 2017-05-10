<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TargetUserProgress\TargetUserProgressTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress\UserProgressTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\PanelRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Lists the users for this learning path with their progress
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressComponent extends BaseReportingComponent implements TableSupport
{
    /**
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    function build()
    {
        $panelRenderer = new PanelRenderer();
        $translator = Translation::getInstance();

        $html = array();
        $html[] = $this->render_header();
        $html[] = $this->renderCommonFunctionality();
        $html[] = $this->renderTargetStatistics($panelRenderer, $translator);

        /*$table = new UserProgressTable($this);
        $table->setSearchForm($this->getSearchButtonToolbarRenderer()->getSearchForm());

        $html[] = $panelRenderer->render(
            $translator->getTranslation('UserAttempts'),
            $this->getSearchButtonToolbarRenderer()->render() . $table->as_html()
        );*/

        $table = new TargetUserProgressTable($this);
        $table->setSearchForm($this->getSearchButtonToolbarRenderer()->getSearchForm());

        $html[] = $panelRenderer->render(
            $translator->getTranslation('TargetUsers'),
            $this->getSearchButtonToolbarRenderer()->render() . $table->as_html()
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the statistics for the target users
     *
     * @param PanelRenderer $panelRenderer
     * @param Translation $translator
     *
     * @return array
     */
    protected function renderTargetStatistics(PanelRenderer $panelRenderer, Translation $translator)
    {
        $html = array();

        $trackingService = $this->getLearningPathTrackingService();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-3">';

        $html[] = $panelRenderer->render(
            $translator->getTranslation('TargetUsers'),
            $trackingService->countTargetUsers($this->get_root_content_object())
        );

        $html[] = '</div>';
        $html[] = '<div class="col-sm-3">';

        $html[] = $panelRenderer->render(
            $translator->getTranslation('TargetUsersWithoutAttempts'),
            $trackingService->countTargetUsersWithoutLearningPathAttempts($this->get_root_content_object())
        );

        $html[] = '</div>';
        $html[] = '<div class="col-sm-3">';

        $html[] = $panelRenderer->render(
            $translator->getTranslation('TargetUsersWithFullAttempts'),
            $trackingService->countTargetUsersWithFullLearningPathAttempts(
                $this->get_root_content_object(), $this->getCurrentLearningPathTreeNode()
            )
        );

        $html[] = '</div>';
        $html[] = '<div class="col-sm-3">';

        $html[] = $panelRenderer->render(
            $translator->getTranslation('TargetUsersWithPartialAttempts'),
            $trackingService->countTargetUsersWithPartialLearningPathAttempts($this->get_root_content_object())
        );

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return ButtonToolBarRenderer
     */
    protected function getSearchButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($table_class_name)
    {
        return $this->getSearchButtonToolbarRenderer()->getConditions(
            array(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL)
            )
        );
    }
}