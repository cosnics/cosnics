<?php
namespace Chamilo\Libraries\Format\Tabs;

class DynamicFormElementsTab extends DynamicTab
{

    private $element;

    /**
     *
     * @param $id integer
     * @param $name string
     * @param $image string
     * @param $content string
     */
    public function __construct($id, $name, $image, $element)
    {
        parent::__construct($id, $name, $image);
        $this->element = $element;
    }

    /**
     *
     * @return the $content
     */
    public function get_element()
    {
        return $this->element;
    }

    /**
     *
     * @param $content the $content to set
     */
    public function set_element($element)
    {
        $this->element = $element;
    }

    public function get_link()
    {
        return '#' . $this->get_id();
    }

    /**
     *
     * @param $tab_name string
     * @return string
     */
    public function body($isOnlyTab = false)
    {
        return '';
    }
}
