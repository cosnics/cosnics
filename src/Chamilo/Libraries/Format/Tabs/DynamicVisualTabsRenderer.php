<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DynamicVisualTabsRenderer extends DynamicTabsRenderer
{

    /**
     *
     * @var string
     */
    private $content;

    /**
     *
     * @param string $name
     * @param string $content
     */
    public function __construct($name, $content = '')
    {
        parent::__construct($name);
        $this->content = $content;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer::render()
     */
    public function render()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->content;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public static function body_footer()
    {
        return '</div>';
    }

    /**
     *
     * @return string
     */
    public static function body_header()
    {
        return '<div class="dynamic_visual_tab">';
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer::footer()
     */
    public function footer()
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     *
     * @param string $content
     */
    public function set_content($content)
    {
        $this->content = $content;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer::header()
     */
    public function header()
    {
        $tabs = $this->get_tabs();

        $html = [];

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

    /**
     *
     * @return string
     */
    public function renderFooter()
    {
        return $this->footer();
    }

    /**
     *
     * @return string
     */
    public function renderHeader()
    {
        return $this->header();
    }
}
