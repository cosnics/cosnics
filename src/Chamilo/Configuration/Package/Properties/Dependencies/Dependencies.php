<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies;

use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @author Hans De Bisschop
 * @package core.lynx
 */
class Dependencies
{
    const PROPERTY_DEPENDENCIES = 'dependencies';

    /**
     *
     * @var int
     */
    private $operator;

    /**
     *
     * @var multitype:\configuration\package\Dependency
     */
    private $dependencies;

    /**
     *
     * @var \libraries\format\MessageLogger
     */
    protected $logger;

    /**
     *
     * @param int $operator
     * @param \configuration\package\Dependency $dependencies
     */
    public function __construct($dependencies = array())
    {
        $this->set_dependencies($dependencies);

        $this->logger = MessageLogger::getInstance($this);
    }

    /**
     *
     * @return \libraries\format\MessageLogger
     */
    public function get_logger()
    {
        return $this->logger;
    }

    /**
     *
     * @return multitype:\configuration\package\Dependency
     */
    public function get_dependencies()
    {
        return $this->dependencies;
    }

    /**
     *
     * @param multitype:\configuration\package\Dependency $dependencies
     */
    public function set_dependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     *
     * @param \configuration\package\Dependency $dependency
     */
    public function add_dependency($dependency)
    {
        $this->dependencies[] = $dependency;
    }

    /**
     *
     * @return string
     */
    public function as_html()
    {
        $html = array();

        foreach ($this->get_dependencies() as $dependency)
        {
            $html[] = $dependency->as_html();
        }

        return implode(' ' . Translation::get('And') . ' ', $html);
    }

    /**
     *
     * @return boolean
     */
    public function check()
    {
        $success = 0;
        $messages = array();

        foreach ($this->get_dependencies() as $dependency)
        {
            if ($dependency->check())
            {
                $success ++;
            }

            $messages[] = $dependency->get_logger()->render();
        }

        $result = ($success == count($this->get_dependencies()));

        $operator = Translation::get('And');

        $this->get_logger()->add_message(implode(' ' . $operator . ' ', $messages));

        return $result;
    }

    public function needs($context)
    {
        foreach ($this->get_dependencies() as $dependency)
        {
            if ($dependency->needs($context))
            {
                return true;
            }
        }
        return false;
    }
}
