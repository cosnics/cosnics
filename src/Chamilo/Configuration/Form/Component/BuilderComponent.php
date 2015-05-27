<?php
namespace Chamilo\Configuration\Form\Component;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Table\Element\ElementTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
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
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->display_element_types();
        $html[] = '<br /><br />';
        $html[] = $this->display_element_table();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function display_element_table()
    {
        $table = new ElementTable($this);
        return $table->as_html();
    }

    public function display_element_types()
    {
        $html[] = '<div class="category_form"><div id="content_object_selection">';

        foreach (Element :: get_types() as $typename => $typevalue)
        {
            $link = $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_ADD_FORM_ELEMENT,
                    self :: PARAM_DYNAMIC_FORM_ELEMENT_TYPE => $typevalue));
            $html[] = '<a href="' . $link . '"><div class="create_block" style="background-image: url(' .
                 Theme :: getInstance()->getImagePath('Chamilo\Configuration\Form', 'Elements/FormType' . $typevalue) . ');">';
            $html[] = $typename;
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
        }

        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
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
        return new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($this->get_form()->get_id()));
    }
}
