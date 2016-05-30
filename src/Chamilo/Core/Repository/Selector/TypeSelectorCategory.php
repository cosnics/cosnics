<?php
namespace Chamilo\Core\Repository\Selector;

/**
 * A category of options in a ContentObjectTypeSelector
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TypeSelectorCategory
{

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var \core\repository\ContentObjectTypeSelectorOption[]
     */
    private $options;

    /**
     *
     * @param string $type
     * @param string $name
     * @param \core\repository\ContentObjectTypeSelectorOption[] $options
     */
    public function __construct($type, $name, $options = array())
    {
        $this->type = $type;
        $this->name = $name;
        $this->options = $options;
    }

    /**
     *
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     */
    public function set_type($type)
    {
        $this->type = $type;
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
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Selector\TypeSelectorOption[]
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     *
     * @param \core\repository\ContentObjectTypeSelectorOption[]
     */
    public function set_options($options)
    {
        $this->options = $options;
    }

    /**
     *
     * @param \core\repository\ContentObjectTypeSelectorOption $option
     */
    public function add_option($option)
    {
        $this->options[] = $option;
    }

    /**
     * Sort the ContentObjectTypeSelectorOption instances by name
     */
    public function sort()
    {
        usort(
            $this->options, 
            function ($option_a, $option_b)
            {
                return strcmp($option_a->get_name(), $option_b->get_name());
            });
    }

    /**
     *
     * @return int
     */
    public function count()
    {
        return count($this->get_options());
    }

    /**
     *
     * @return int[]
     */
    public function get_unique_content_object_template_ids()
    {
        $types = array();
        
        foreach ($this->get_options() as $option)
        {
            if (! in_array($option->get_template_registration_id(), $types))
            {
                $types[] = $option->get_template_registration_id();
            }
        }
        
        return $types;
    }
}