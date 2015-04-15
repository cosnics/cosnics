<?php
namespace Chamilo\Libraries\Format\Tabs;

class DynamicVisualTabsRenderer extends DynamicTabsRenderer
{

    private $content;

    public function __construct($name, $content = '')
    {
        parent :: __construct($name);
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
        
        // $html[] = '<a name="top"></a>';
        $html[] = '<div id="' . $this->get_name() . '_tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">';
        
        // Tab headers
        $html[] = '<ul id="' . $this->get_name() .
             '_visual_tabs" style="display: block;" class="tabs-header ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">';
        foreach ($tabs as $key => $tab)
        {
            $html[] = $tab->header();
        }
        $html[] = '</ul>';
        
        return implode(PHP_EOL, $html);
    }

    public function footer()
    {
        $html = array();
        $html[] = '</div>';
        
        $html[] = '<script type="text/javascript">
		$("#' . $this->get_name() . '_visual_tabs").dynamic_tabs( {
                cycle : false,
                follow : false,
                nextButton : ">>",
                prevButton : "<<"
        });
        </script>
        ';
        
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

    public function renderHeader(){

        $html = array();
        $html[] = $this->header();
        $html[] = self :: body_header();

        return implode(PHP_EOL, $html);
        
    }
    
    public function renderFooter(){
        
        $html = array();
            
        $html[] = self :: body_footer();
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
