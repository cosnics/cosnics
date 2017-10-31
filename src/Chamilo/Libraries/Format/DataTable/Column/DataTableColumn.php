<?php
namespace Chamilo\Libraries\Format\DataTable\Column;

/**
 *
 * @package Chamilo\Libraries\Format\Table\Column
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
abstract class DataTableColumn
{

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $title;

    /**
     *
     * @param string $name
     * @param string $title
     */
    public function __construct($name, $title)
    {
        $this->name = $name;
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
