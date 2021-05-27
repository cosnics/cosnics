<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DynamicActionsTab extends DynamicTab
{

    /**
     * @var \Chamilo\Libraries\Format\Tabs\DynamicAction[]
     */
    private $actions;

    /**
     *
     * @param integer $id
     * @param string $name
     * @param string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $image
     * @param \Chamilo\Libraries\Format\Tabs\DynamicAction[] $actions
     */
    public function __construct($id, $name, $image, $actions = [])
    {
        parent::__construct($id, $name, $image);
        $this->actions = $actions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicAction $action
     */
    public function add_action(DynamicAction $action)
    {
        $this->actions[] = $action;
    }

    /**
     * @param boolean $isOnlyTab
     *
     * @return string
     */
    public function body($isOnlyTab = false)
    {
        $html = [];

        $html[] = $this->body_header();

        foreach ($this->actions as $key => $action)
        {
            $html[] = $action->render($key == 0);
        }

        $html[] = $this->body_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTab::body_header()
     */
    public function body_header()
    {
        $html = [];

        $html[] = '<div role="tabpanel" class="tab-pane" id="' . $this->get_id() . '">';
        $html[] = '<div class="list-group">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\DynamicAction[]
     */
    public function get_actions()
    {
        return $this->actions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicAction[] $actions
     */
    public function set_actions($actions)
    {
        $this->actions = $actions;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTab::get_link()
     */
    public function get_link()
    {
        return '#' . $this->get_id();
    }
}
