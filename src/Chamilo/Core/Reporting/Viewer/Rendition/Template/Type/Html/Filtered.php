<?php
/**
 * Created by PhpStorm.
 * User: tomgoethals Date: 10/03/14 Time: 14:19
 */
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Html;

use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Core\Reporting\FilteredBlock;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Manager;

class Filtered extends Basic
{
    /*
     * public function get_action_bar() { $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
     * $parameters = $this->get_context()->get_parameters(); $parameters[Manager :: PARAM_ACTION] = Manager ::
     * ACTION_VIEW; if ($this->get_context()->show_all()) { $parameters[Manager :: PARAM_SHOW_ALL] = null;
     * $action_bar->add_tool_action( new ToolbarItem( Translation :: get('ShowOne'), Theme ::
     * get_image_path(__NAMESPACE__) . 'action/show_block.png', $this->get_context()->get_url($parameters))); } else {
     * if ($this->get_template()->count_blocks() > 1) { $parameters[Manager :: PARAM_SHOW_ALL] = 1;
     * $action_bar->add_tool_action( new ToolbarItem( Translation :: get('ShowAll'), Theme ::
     * get_image_path(__NAMESPACE__) . 'action/show_all.png', $this->get_context()->get_url($parameters))); } }
     * $parameters = $this->get_context()->get_parameters(); $parameters[Manager :: PARAM_ACTION] = Manager ::
     * ACTION_SAVE; $parameters[Manager :: PARAM_SHOW_ALL] = 1; $parameters[Manager :: PARAM_FORMAT] = TemplateRendition
     * :: FORMAT_XLSX; $action_bar->add_common_action( new ToolbarItem( Translation :: get('ExportToExcel'), Theme ::
     * get_common_image_path() . 'export_excel.png', $this->get_context()->get_url($parameters))); $parameters =
     * $this->get_context()->get_parameters(); $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_SAVE;
     * $parameters[Manager :: PARAM_SHOW_ALL] = 1; $parameters[Manager :: PARAM_FORMAT] = TemplateRendition ::
     * FORMAT_ODS; $action_bar->add_common_action( new ToolbarItem( Translation :: get('ExportToOds'), Theme ::
     * get_common_image_path() . 'export_ods.png', $this->get_context()->get_url($parameters))); return $action_bar; }
     */
    public function render_block()
    {
        if ($this->show_all())
        {
            $this->get_context()->set_parameter(Manager :: PARAM_SHOW_ALL, 1);
            $this->get_context()->set_parameter(Manager :: PARAM_VIEWS, $this->get_context()->get_current_view());
            
            $html = array();
            
            foreach ($this->get_template()->get_blocks() as $key => $block)
            {
                $title = Translation :: get(
                    ClassnameUtilities :: getInstance()->getClassnameFromObject($block), 
                    null, 
                    ClassnameUtilities :: getInstance()->getNamespaceFromObject($block));
                
                if ($block instanceof FilteredBlock)
                {
                    $parameters = $this->get_context()->get_parameters();
                    $url = $this->get_context()->get_url($parameters);
                    $html[] = $block->get_form($url)->toHtml();
                }
                
                $html[] = '<h2>';
                $html[] = '<img style="vertical-align: middle;" src="' .
                     Theme :: getInstance()->getImagePath($block->context()) . ClassnameUtilities :: getInstance()->getClassnameFromObject(
                        $block, 
                        true) . '.png' . '"/> ';
                $html[] = $title;
                $html[] = '</h2>';
                $html[] = BlockRenditionImplementation :: launch(
                    $this, 
                    $block, 
                    $this->get_format(), 
                    $this->determine_current_block_view($key));
            }
            
            return implode("\n", $html);
        }
        else
        {
            $current_block_id = $this->determine_current_block_id();
            $current_block = $this->get_template()->get_block($current_block_id);
            // $this->get_context()->set_parameter(Manager :: PARAM_BLOCK_ID, $current_block_id);
            
            $html = array();
            if ($current_block instanceof FilteredBlock)
            {
                $parameters = $this->get_context()->get_parameters();
                $parameters[Manager :: PARAM_BLOCK_ID] = $current_block_id;
                $url = $this->get_context()->get_url($parameters);
                $html[] = $current_block->get_form($url)->toHtml();
            }
            
            $html[] = $rendered_block = BlockRenditionImplementation :: launch(
                $this, 
                $current_block, 
                $this->get_format(), 
                $this->determine_current_block_view($current_block_id));
            
            if ($this->get_template()->count_blocks() > 1)
            {
                $tabs = new DynamicVisualTabsRenderer(
                    ClassnameUtilities :: getInstance()->getClassnameFromObject($this->get_template(), true), 
                    implode("\n", $html));
                
                $context_parameters = $this->get_context()->get_parameters();
                
                $trail = BreadcrumbTrail :: get_instance();
                
                foreach ($this->get_template()->get_blocks() as $key => $block)
                {
                    $block_parameters = array_merge($context_parameters, array(Manager :: PARAM_BLOCK_ID => $key));
                    
                    $is_current_block = $key == $this->determine_current_block_id() ? true : false;
                    
                    $title = Translation :: get(
                        ClassnameUtilities :: getInstance()->getClassnameFromObject($block), 
                        null, 
                        ClassnameUtilities :: getInstance()->getNamespaceFromObject($block));
                    
                    if ($is_current_block)
                    {
                        $trail->add(new Breadcrumb($this->get_context()->get_url($block_parameters), $title));
                    }
                    
                    $tabs->add_tab(
                        new DynamicVisualTab(
                            $key, 
                            $title, 
                            Theme :: getInstance()->getImagePath($block->context()) . ClassnameUtilities :: getInstance()->getClassnameFromObject(
                                $block, 
                                true) . '.png', 
                            $this->get_context()->get_url($block_parameters), 
                            $is_current_block));
                }
                
                return $tabs->render();
            }
            else
            {
                return implode("\n", $html);
            }
        }
    }
}