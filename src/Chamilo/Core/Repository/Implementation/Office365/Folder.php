<?php
namespace Chamilo\Core\Repository\Implementation\Office365;

class Folder
{

    private $id;

    private $title;

    private $parent;

    private $children = array();

    /**
     *
     * @return the $parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *
     * @param field_type $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     *
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function addChild($child)
    {
        $this->children[] = $child;
    }
}
