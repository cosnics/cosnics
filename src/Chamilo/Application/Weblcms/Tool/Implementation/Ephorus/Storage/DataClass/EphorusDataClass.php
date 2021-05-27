<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Core\DependencyContainer;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class extends the DataClass to add extra common functionality
 *
 * @package application\weblcms\tool\ephorus;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EphorusDataClass extends DataClass
{
    const DEPENDENCY_DATA_MANAGER_CLASS = 'data_manager_class';
    const DEPENDENCY_STRING_UTILITIES_CLASS = 'string_utilities_class';

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * The dependency container
     *
     * @var DependencyContainer
     */
    private $dependency_container;

    /**
     * **************************************************************************************************************
     * Main functionality *
     * **************************************************************************************************************
     */

    /**
     * Constructor Initializes the dependency container if it's not given
     */
    public function __construct(
        $default_properties = [], $optional_properties = [], DependencyContainer $dependency_container = null
    )
    {
        parent::__construct($default_properties, $optional_properties);

        if (is_null($dependency_container))
        {
            $dependency_container = new DependencyContainer();
        }

        $this->set_dependency_container($dependency_container);

        $this->initialize_dependencies($dependency_container);
    }

    /**
     * Returns the data manager class dependency
     *
     * @return string
     */
    public function get_data_manager_class()
    {
        return $this->get_dependency(self::DEPENDENCY_DATA_MANAGER_CLASS);
    }

    // @codeCoverageIgnoreStart

    /**
     * **************************************************************************************************************
     * Dependency getters & setters *
     * **************************************************************************************************************
     */

    /**
     * Returns a dependency with a given name
     *
     * @return mixed
     */
    public function get_dependency($dependency_name)
    {
        return $this->get_dependency_container()->get($dependency_name);
    }

    /**
     * Returns the dependency container
     *
     * @return DependencyContainer
     */
    public function get_dependency_container()
    {
        return $this->dependency_container;
    }

    /**
     * Sets the dependency container
     *
     * @param DependencyContainer $dependency_container
     */
    public function set_dependency_container(DependencyContainer $dependency_container)
    {
        $this->dependency_container = $dependency_container;
    }

    /**
     * Returns the string utilities class dependency
     *
     * @return string
     */
    public function get_string_utilities_class()
    {
        return $this->get_dependency(self::DEPENDENCY_STRING_UTILITIES_CLASS);
    }

    /**
     * **************************************************************************************************************
     * Delegation Functionality *
     * **************************************************************************************************************
     */

    /**
     * Initializes the dependencies
     *
     * @param DependencyContainer $dependency_container
     */
    public function initialize_dependencies(DependencyContainer $dependency_container)
    {
        $dependency_container->add(
            self::DEPENDENCY_DATA_MANAGER_CLASS,
            'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager'
        );
        $dependency_container->add(
            self::DEPENDENCY_STRING_UTILITIES_CLASS, 'Chamilo\Libraries\Utilities\StringUtilities'
        );
    }

    /**
     * Replaces an existing dependency with a new dependency
     *
     * @param string $dependency_name
     * @param mixed $dependency
     */
    public function replace_dependency($dependency_name, $dependency)
    {
        return $this->get_dependency_container()->replace($dependency_name, $dependency);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Sets the data manager class dependency
     *
     * @param string $data_manager_class
     */
    public function set_data_manager_class($data_manager_class)
    {
        $this->replace_dependency(self::DEPENDENCY_DATA_MANAGER_CLASS, $data_manager_class);
    }

    /**
     * Sets the string utilities class dependency
     *
     * @param string $data_manager_class
     */
    public function set_string_utilities_class($string_utilities_class)
    {
        $this->replace_dependency(self::DEPENDENCY_STRING_UTILITIES_CLASS, $string_utilities_class);
    }
    // @codeCoverageIgnoreStop
}
