<?php
namespace Chamilo\Core\Repository\Common\Renderer;

use Chamilo\Configuration\Category\Form\ImpactViewForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Table\ImpactView\ImpactViewTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Renderer to render the impact viewer
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImpactViewRenderer implements TableSupport
{

    /**
     *
     * @var Condition
     */
    private $impact_view_table_condition;

    /**
     *
     * @var ImpactViewForm
     */
    private $form;

    /**
     *
     * @var array
     */
    private $co_ids;

    /**
     *
     * @var \Chamilo\Core\Repository\Manager
     */
    private $parent;

    private $has_impact;

    public function validated()
    {
        return $this->form->validate();
    }

    /**
     * Constructs the impact view (form).
     */
    public function __construct(\Chamilo\Core\Repository\Manager $parent, array $co_ids, $has_impact)
    {
        $this->parent = $parent;
        $this->co_ids = $co_ids;
        $this->has_impact = $has_impact;
        
        $this->form = new ImpactViewForm(
            $this->parent->get_url(
                array(\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $co_ids)), 
            $has_impact);
    }

    /**
     * Renders the impact view using the specified condition for content objects.
     * 
     * @param Condition $co_condition
     *
     * @return string
     */
    public function render(Condition $co_condition)
    {
        if ($this->has_impact)
        {
            $view = $this->render_impact_view($co_condition);
        }
        else
        {
            $view = '<div class="normal-message">' . Translation::get('NoImpact', array(), 'Chamilo\Core\Repository') .
                 '</div>';
        }
        
        $html = array();
        
        $html[] = $this->parent->render_header();
        $html[] = $view;
        $html[] = $this->form->toHtml();
        $html[] = $this->parent->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_parameters()
    {
        return $this->parent->get_parameters();
    }

    public function get_url($parameters = array(), $filter = array(), $encode_entities = false)
    {
        return $this->parent->get_url($parameters, $filter, $encode_entities);
    }

    /**
     * **************************************************************************************************************
     * Inherited
     * **************************************************************************************************************
     */
    
    /**
     * Get the condition for the impact view table.
     * 
     * @param $class_name
     * @return Condition
     */
    public function get_table_condition($class_name)
    {
        return $this->impact_view_table_condition;
    }

    /**
     * Renders the impact view table.
     * 
     * @param $condition
     * @return string
     */
    private function render_impact_view(Condition $condition)
    {
        $this->impact_view_table_condition = $condition;
        $impact_view_table = new ImpactViewTable($this);
        
        return $impact_view_table->as_html();
    }

    /**
     * **************************************************************************************************************
     * Impact view table requirements (not in interface)
     * **************************************************************************************************************
     */
    
    /**
     * Returns the url to the content object preview.
     * 
     * @param ContentObject $content_object
     *
     * @return string
     */
    public function get_content_object_preview_url(ContentObject $content_object)
    {
        return $this->parent->get_url(
            array(
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS, 
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(), 
                \Chamilo\Core\Repository\Manager::PARAM_CATEGORY_ID => $content_object->get_parent_id()));
    }
}

