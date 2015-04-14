<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Libraries\Platform\Translation;
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_AJAX_CONTEXT = 'ajax_context';
    
    // Default action
    const DEFAULT_ACTION = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
    
    /**
     *
     * @var int
     */
    private $current_step;
    
    /**
     *
     * @var \libraries\format\DynamicVisualTabsRenderer
     */
    private $tabs_renderer;
    
    public function run()
    {
        $page = $this->get_parent()->get_root_content_object();
    
        if (! $page)
        {
            return $this->display_error_page(Translation :: get('NoObjectSelected'));
        }
        
    }
    
    /**
     * Get the id of the currently requested step
     *
     * @return int
     */
    public function get_current_step()
    {
        if (! isset($this->current_step))
        {
            $this->current_step = $this->getRequest()->get(self :: PARAM_STEP) ? $this->getRequest()->get(self :: PARAM_STEP) : 1;
            if (is_array($this->current_step))
            {
                $this->current_step = $this->current_step[0];
            }
        }
    
        return $this->current_step;
    }
    
    /**
     * Get the content object linked to the current step
     *
     * @return \core\repository\ContentObject
     */
    public function get_current_content_object()
    {
        return $this->get_current_node()->get_content_object();
    }
    
    /**
     * Get the complex content object item linked to the current step
     *
     * @return \core\repository\storage\data_class\ComplexContentObjectItem
     */
    public function get_current_complex_content_object_item()
    {
        return $this->get_current_node()->get_complex_content_object_item();
    }
    
    /**
     * Get the TabsRenderer
     *
     * @return \libraries\format\DynamicVisualTabsRenderer
     */
    public function get_tabs_renderer()
    {
        return $this->tabs_renderer;
    }
    
    /**
     * Get the node linked to the current step
     *
     * @return \core\repository\common\path\ComplexContentObjectPathNode
     */
    public function get_current_node()
    {
        return $this->get_parent()->get_root_content_object()->get_complex_content_object_path()->get_node(
            $this->get_current_step());
    }
}