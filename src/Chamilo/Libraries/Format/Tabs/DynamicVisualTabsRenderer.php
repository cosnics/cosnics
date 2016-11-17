<?php
namespace Chamilo\Libraries\Format\Tabs;

class DynamicVisualTabsRenderer extends DynamicTabsRenderer
{

    private $content;

    public function __construct($name, $content = '')
    {
        parent::__construct($name);
        $this->content = $content;
    }

    /**
     *
     * @return the $content
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     *
     * @param $content the content to set
     */
    public function set_content($content)
    {
        $this->content = $content;
    }

    public function header()
    {
        $tabs = $this->get_tabs();
        
        $html = array();
        
        $html[] = '<ul class="nav nav-tabs dynamic-visual-tabs">';
        
        foreach ($tabs as $key => $tab)
        {
            $html[] = $tab->header();
        }
        
        $html[] = '</ul>';
        $html[] = '<div class="dynamic-visual-tab-content">';
        $html[] = '<div class="list-group-item">';
        
        return implode(PHP_EOL, $html);
    }

    public function footer()
    {
        $html = array();
        
        $html[] = '</div>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    public function render()
    {
        $html = array();
        
        $html[] = $this->renderHeader();
        $html[] = $this->content;
        $html[] = $this->renderFooter();
        
        return implode(PHP_EOL, $html);
    }

    public function renderHeader()
    {
        $html = array();
        $html[] = $this->header();
        
        return implode(PHP_EOL, $html);
    }

    public function renderFooter()
    {
        $html = array();
        
        $html[] = $this->footer();
        
        return implode(PHP_EOL, $html);
    }

    public static function body_header()
    {
        $html = array();
        
        $html[] = '<div class="admin_tab ui-tabs-panel ui-widget-content ui-corner-bottom dynamic_visual_tab">';
        $html[] = '<a class="prev"></a>';
        
        return implode(PHP_EOL, $html);
    }

    public static function body_footer()
    {
        $html = array();
        
        $html[] = '<a class="next"></a>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
