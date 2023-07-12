<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Translation\Translation;

abstract class ReportingBlock
{
    use DependencyInjectionContainerTrait;

    // Constants
    public const PARAM_DISPLAY_MODE = 'display_mode';

    private $data;

    private $id;

    private $params;

    private $parent;

    private $vertical;

    public function __construct($parent, $vertical = false)
    {
        $this->parent = $parent;
        $this->vertical = $vertical;
    }

    public function add_function_parameter($key, $value)
    {
        $this->params[$key] = $value;
    }

    abstract public function count_data();

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurationConsulter::class
        );
    }

    /**
     * @brief Return block style containing properties such as title font size or title color.
     * Default implementation
     * retrieves values from the cental configuration. See src/Chamilo/Core/Reporting/Resources/Settings/settings.xml.
     * Blocks can override this function and return a ReportingBlockStyle object with custom properties.
     */
    public function getStyle()
    {
        return new ReportingBlockStyle($this->getConfigurationConsulter());
    }

    /**
     * Getters and setters
     */
    public function get_data()
    {
        if (!$this->data)
        {
            $this->data = $this->retrieve_data();
        }

        return $this->data;
    }

    public function get_function_parameters()
    {
        return $this->params;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(static::class);
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function get_title()
    {
        return Translation::get(
            ClassnameUtilities::getInstance()->getClassnameFromObject($this), null,
            ClassnameUtilities::getInstance()->getNamespaceFromObject($this)
        );
    }

    abstract public function get_views();

    public function remove_function_parameter($key)
    {
        unset($this->params[$key]);
    }

    /**
     * @return \Chamilo\Core\Reporting\ReportingData
     */
    abstract public function retrieve_data();

    public function set_function_parameters($params)
    {
        $this->params = $params;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }
}
