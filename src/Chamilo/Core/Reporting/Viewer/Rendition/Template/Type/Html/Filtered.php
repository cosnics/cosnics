<?php
/**
 * Created by PhpStorm.
 * User: tomgoethals Date: 10/03/14 Time: 14:19
 */
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Html;

use Chamilo\Core\Reporting\FilteredBlock;
use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class Filtered extends Basic
{

    public function render_block()
    {
        if ($this->show_all())
        {
            $this->get_context()->set_parameter(Manager::PARAM_SHOW_ALL, 1);
            $this->get_context()->set_parameter(Manager::PARAM_VIEWS, $this->get_context()->get_current_view());
            
            $html = array();
            
            foreach ($this->get_template()->get_blocks() as $key => $block)
            {
                $title = Translation::get(
                    ClassnameUtilities::getInstance()->getClassnameFromObject($block), 
                    null, 
                    ClassnameUtilities::getInstance()->getNamespaceFromObject($block));
                
                if ($block instanceof FilteredBlock)
                {
                    $parameters = $this->get_context()->get_parameters();
                    $url = $this->get_context()->get_url($parameters);
                    $html[] = $block->get_form($url)->toHtml();
                }
                
                $html[] = '<h2>';
                $html[] = '<img style="vertical-align: middle;" src="' . Theme::getInstance()->getImagePath(
                    $this->getBlockNamespace($block), 
                    ClassnameUtilities::getInstance()->getClassnameFromObject($block)) . '"/> ';
                $html[] = $title;
                $html[] = '</h2>';
                $html[] = BlockRenditionImplementation::launch(
                    $this, 
                    $block, 
                    $this->get_format(), 
                    $this->determine_current_block_view($key));
            }
            
            return implode(PHP_EOL, $html);
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
                $parameters[Manager::PARAM_BLOCK_ID] = $current_block_id;
                $url = $this->get_context()->get_url($parameters);
                $html[] = $current_block->get_form($url)->toHtml();
            }
            
            $html[] = $rendered_block = BlockRenditionImplementation::launch(
                $this, 
                $current_block, 
                $this->get_format(), 
                $this->determine_current_block_view($current_block_id));
            
            if ($this->get_template()->count_blocks() > 1)
            {
                $tabs = new DynamicVisualTabsRenderer(
                    ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template(), true), 
                    implode(PHP_EOL, $html));
                
                $context_parameters = $this->get_context()->get_parameters();
                
                $trail = BreadcrumbTrail::getInstance();
                
                foreach ($this->get_template()->get_blocks() as $key => $block)
                {
                    $block_parameters = array_merge($context_parameters, array(Manager::PARAM_BLOCK_ID => $key));
                    
                    $is_current_block = $key == $this->determine_current_block_id() ? true : false;
                    
                    $title = Translation::get(
                        ClassnameUtilities::getInstance()->getClassnameFromObject($block), 
                        null, 
                        ClassnameUtilities::getInstance()->getNamespaceFromObject($block));
                    
                    if ($is_current_block)
                    {
                        $trail->add(new Breadcrumb($this->get_context()->get_url($block_parameters), $title));
                    }
                    
                    $tabs->add_tab(
                        new DynamicVisualTab(
                            $key, 
                            $title, 
                            Theme::getInstance()->getImagePath(
                                $this->getBlockNamespace($block), 
                                ClassnameUtilities::getInstance()->getClassnameFromObject($block)), 
                            $this->get_context()->get_url($block_parameters), 
                            $is_current_block));
                }
                
                return $tabs->render();
            }
            else
            {
                return implode(PHP_EOL, $html);
            }
        }
    }
}