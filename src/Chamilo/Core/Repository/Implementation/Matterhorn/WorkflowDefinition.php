<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

class WorkflowDefinition
{

    private $id;

    private $title;

    private $description;

    private $operations;

    /**
     *
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @return the $title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @return the $description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     *
     * @return the $operations
     */
    public function get_operations()
    {
        return $this->operations;
    }

    /**
     *
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param $title the $title to set
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @param $description the $description to set
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @param $operations the $operations to set
     */
    public function set_operations($operations)
    {
        $this->operations = $operations;
    }
}
