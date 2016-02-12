<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class represents a column for a table Refactoring from ObjectTable to split between a table based on a record
 * and based on an object (@TODO: Used New in the name because of the fact that there is currently a class with the name
 * TableColumn)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableColumn
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * The name of the column
     *
     * @var string
     */
    private $name;

    /**
     * The visible title of the column in the layout
     */
    private $title;

    /**
     * Whether or not the column is sortable
     *
     * @var bool
     */
    private $sortable;

    /**
     * The CSS Classes
     *
     * @var string
     */
    protected $cssClasses;

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param string $name
     * @param string $title - [OPTIONAL] default null - translation of the column name
     * @param bool $sortable - [OPTIONAL] default null
     * @param string $cssClasses
     */
    public function __construct($name, $title = null, $sortable = true, $cssClasses = null)
    {
        $this->set_name($name);

        if (is_null($title))
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            $index = 1;

            do
            {
                $called_class = $backtrace[$index]['class'];
                $context = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($called_class);
                $index ++;
            }
            while ($context == Utilities :: COMMON_LIBRARIES);

            $title = Translation :: get((string) StringUtilities :: getInstance()->createString($name)->upperCamelize(), array(), $context);
        }

        $this->set_title($title);
        $this->set_sortable($sortable);
        $this->setCssClasses($cssClasses);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the name of this column
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Sets the name of this column
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the title of this column
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Sets the title of this column
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     * Returns if this column is sortable
     *
     * @return boolean
     */
    public function is_sortable()
    {
        return $this->sortable;
    }

    /**
     * Sets if this column is sortable
     *
     * @param boolean $sortable
     */
    public function set_sortable($sortable)
    {
        $this->sortable = $sortable;
    }

    /**
     * @return string
     */
    public function getCssClasses()
    {
        return $this->cssClasses;
    }

    /**
     * @param string $cssClasses
     */
    public function setCssClasses($cssClasses)
    {
        $this->cssClasses = $cssClasses;
    }

    /**
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
