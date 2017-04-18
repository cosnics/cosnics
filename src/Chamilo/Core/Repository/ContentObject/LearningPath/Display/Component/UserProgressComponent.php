<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
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
class UserProgressComponent extends Manager implements TableSupport
{
    /**
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    /**
     * @return string
     */
    function run()
    {
        $panelRenderer = new PanelRenderer();
        $translator = Translation::getInstance();

        $html = array();
        $html[] = $this->render_header();

        $table = new UserProgressTable($this);
        $table->setSearchForm($this->getButtonToolbarRenderer()->getSearchForm());

        $html[] = $panelRenderer->render(
            $translator->getTranslation('UserAttempts'),
            $this->getButtonToolbarRenderer()->render() . $table->as_html()
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if(!isset($this->buttonToolbarRenderer))
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
        return $this->getButtonToolbarRenderer()->getConditions(
            array(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL)
            )
        );
    }
}