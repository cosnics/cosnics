<?php
namespace Chamilo\Libraries\Format\Tabs;

class DynamicActionsTab extends DynamicTab
{

    /**
     *
     * @param integer $id
     * @param string $name
     * @param string $image
     * @param Array $actions
     */
    public function __construct($id, $name, $image, $actions = array())
    {
        parent :: __construct($id, $name, $image);
        $this->actions = $actions;
    }

    /**
     *
     * @return the $content
     */
    public function get_actions()
    {
        return $this->actions;
    }

    /**
     *
     * @param $content the $content to set
     */
    public function set_actions($actions)
    {
        $this->actions = $actions;
    }

    public function add_action(DynamicAction $action)
    {
        $this->actions[] = $action;
    }

    public function get_link()
    {
        return '#' . $this->get_id();
    }

    /**
     *
     * @return string
     */
    public function body_header()
    {
        $html = array();

        $html[] = '<div role="tabpanel" class="tab-pane" id="' . $this->get_id() . '">';
        $html[] = '<div class="list-group">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $tab_name
     * @return string
     */
    public function body($isOnlyTab = false)
    {
        $html = array();

        $html[] = $this->body_header();

        foreach ($this->actions as $key => $action)
        {
            $html[] = $action->render($key == 0);
        }

        $html[] = $this->body_footer();

        return implode(PHP_EOL, $html);
    }
}
