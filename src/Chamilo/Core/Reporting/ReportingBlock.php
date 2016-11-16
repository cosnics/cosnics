<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;

abstract class ReportingBlock
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    
    // Constants
    const PARAM_DISPLAY_MODE = "display_mode";

    private $id;

    private $data;

    private $params;

    private $parent;

    private $vertical;

    public function __construct($parent, $vertical = false)
    {
        $this->parent = $parent;
        $this->vertical = $vertical;
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    abstract public function count_data();

    /**
     *
     * @return \core\reporting\ReportingData
     */
    abstract public function retrieve_data();

    public function get_title()
    {
        return Translation::get(static::get_name(), null, static::context());
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(static::class_name());
    }

    abstract public function get_views();

    /**
     * Getters and setters
     */
    public function get_data()
    {
        if (! $this->data)
        {
            $this->data = $this->retrieve_data();
        }
        return $this->data;
    }

    public function add_function_parameter($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function remove_function_parameter($key)
    {
        unset($this->params[$key]);
    }

    public function set_function_parameters($params)
    {
        $this->params = $params;
    }

    public function get_function_parameters()
    {
        return $this->params;
    }

    /**
     * @brief Return block style containing properties such as title font size or title color.
     * Default implementation
     * retrieves values from the cental configuration. See src/Chamilo/Core/Reporting/Resources/Settings/settings.xml.
     * Blocks can override this function and return a ReportingBlockStyle object with custom properties.
     */
    public function getStyle()
    {
        return new ReportingBlockStyle();
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static::context();
    }
}
