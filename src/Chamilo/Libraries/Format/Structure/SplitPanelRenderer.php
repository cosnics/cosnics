<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 * This renderer shows a collection of panels as resizable elements in an accordeon-like construction.
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Tom Goethals
 */
class SplitPanelRenderer
{

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\Panel[]
     */
    private $panels;

    /**
     * Constructs a new SplitPanelRenderer.
     *
     * @param string name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->panels = array();
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Panel[]
     */
    public function get_panels()
    {
        return $this->panels;
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Panel[] $panels
     */
    public function set_panels($panels)
    {
        $this->panels = $panels;
    }

    /**
     *
     * @return integer
     */
    public function size()
    {
        return count($this->panels);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Panel $panel
     */
    public function add_panel(Panel $panel)
    {
        $panel->set_id($this->name . '_' . count($this->panels));
        $this->panels[] = $panel;
    }

    /**
     * Creates the header html for this renderer.
     *
     * @return string
     */
    public function header()
    {
        $html = array();

        $html[] = '<div id="' . $this->name . '_panels">';

        return implode(PHP_EOL, $html);
    }

    /**
     * Creates the footer html for this renderer, also includes javascript for the panels.
     *
     * @return string
     */
    public function footer()
    {
        $html = array();
        $html[] = '</div>';

        $totalwidth = $this->get_panel_widths();
        $baseid = $this->get_name() . '_';

        // TODO: replace this with a decent include once the chamilo included jquery ui has been updated to the right
        // version
        $html[] = '<script src="http://code.jquery.com/ui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>';
        $html[] = '<script type="text/javascript">';
        $html[] = '$(document).ready(function () {
            var npanels = ' . sizeof($this->panels) . ';
            var i;
            for (i = 0; i < npanels - 1; i++) {
                var leftpanel = $("#' . $baseid . '" + i);
                var rightpanel = $("#' . $baseid . '" + (i + 1));
                var minW = 15 * leftpanel.offsetParent().width() / 100;
                leftpanel.resizable({
                    resize: function (event, ui) {
                        var leftid = event.target.id;
                        var rightid = "' .
             $baseid . '" + (parseInt(leftid.substring(' . strlen($baseid) . ')) + 1);
                        var left = $("#" + leftid);
                        var right = $("#" + rightid);';

        if ($this->panels[0]->get_unit() == '%')
        {
            $html[] = '                 var w = getPanelWidth(rightid, ' . $totalwidth .
                 ' * left.offsetParent().width() / 100);';
        }
        else
        {
            $html[] = '                 var w = getPanelWidth(rightid, ' . $totalwidth . ');';
        }

        $html[] = '
                        var minW = 15 * left.offsetParent().width() / 100;
                        if (w < minW) {
                            var diff = minW - w;
                            w =  minW;
                            left.css("width", left.width() - diff);
                        }
                        right.css("width", w);
                    },
                    start: function(event, ui) {
                        var leftid = event.target.id;
                        var rightid = "' .
             $baseid . '" + (parseInt(leftid.substring(' . strlen($baseid) . ')) + 1);
                        var left = $("#" + leftid);
                        var right = $("#" + rightid);
                        //add a mask over the Iframe to prevent IE from stealing mouse events
                        $(\'<div class="ui-draggable-iframeFix" style="background: #fff;"></div>\')
                            .css({
                                width: ' . right .
             '.width()+"px", height: ' . right . '.height()+"px",
                                position: "absolute", opacity: "0.001", zIndex: 1000
                            })
                            .css(' . right . '.offset())
                            .appendTo("body");

                    },
                    stop: function(event, ui) {
                        //remove mask when dragging ends
                        $("div.ui-draggable-iframeFix").remove();
                    }

                });
                leftpanel.resizable("option", "handles", "e");
                leftpanel.resizable("option", "minWidth", minW);
                leftpanel.resizable("option", "minHeight", leftpanel.height());
                leftpanel.resizable("option", "maxHeight", leftpanel.height());
            }
        });';

        $html[] = 'function getPanelWidth(exclude, staticw) {
            var npanels = ' . sizeof($this->panels) . ';
            var width = staticw;
            for (i = 0; i < npanels; i++) {
                if ("' . $baseid . '" + i != exclude) {
                    width -= $("#' . $baseid . '" + i).width();
                }
            }
            return width;
        }';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Calculates the total default width of all panels.
     *
     * @return integer
     */
    private function get_panel_widths()
    {
        $width = 0;

        foreach ($this->panels as $key => $panel)
        {
            $width += $panel->get_width();
        }

        return $width;
    }

    /**
     * Renders the actual body of the accordeon.
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = $this->header();

        // Tab content
        $panels = $this->get_panels();

        foreach ($panels as $key => $panel)
        {
            if ($key == sizeof($panels) - 1)
            {
                $html[] = $panel->body(true);
            }
            else
            {
                $html[] = $panel->body(false);
            }
        }

        $html[] = $this->footer();

        return implode(PHP_EOL, $html);
    }
}
