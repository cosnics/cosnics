<?php
namespace Chamilo\Configuration\Form\Component;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Table\Element\ElementTable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BuilderComponent extends Manager implements TableSupport
{

    public function run()
    {
        $table = new ElementTable($this);

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->display_element_types();
        $html[] = $table->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function display_element_types()
    {
        $buttonToolbar = new ButtonToolBar();

        foreach (Element::get_types() as $typename => $typevalue)
        {
            $link = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_ADD_FORM_ELEMENT,
                    self::PARAM_DYNAMIC_FORM_ELEMENT_TYPE => $typevalue
                )
            );

            $buttonToolbar->addItem(
                new Button(
                    $typename, Element::getTypeGlyph($typevalue), $link, Button::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolbar);

        return $buttonToolBarRenderer->render();
    }

    /**
     * @param string $table_class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    public function get_table_condition($table_class_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($this->get_form()->get_id())
        );
    }
}
