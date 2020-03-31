<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Table\Element\ElementTable;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Executes this controller
     */
    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        if (!$this->getSchemaId())
        {
            throw new NoObjectSelectedException(Translation::get('Schema', null, 'Chamilo\Core\Metadata\Schema'));
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders this components output as html
     */
    public function as_html()
    {
        $table = new ElementTable($this);
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = array();

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('Create', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_CREATE,
                            \Chamilo\Core\Metadata\Schema\Manager::PARAM_SCHEMA_ID => $this->getSchemaId()
                        )
                    )
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $searchCondition = $this->getButtonToolbarRenderer()->getConditions(
            array(new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_NAME))
        );

        if ($searchCondition)
        {
            $conditions[] = $searchCondition;
        }

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_SCHEMA_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($this->getSchemaId())
        );

        return new AndCondition($conditions);
    }
}
