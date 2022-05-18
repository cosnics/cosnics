<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies;

use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Translation\Translation;

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
     * @var \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency[]
     */
    private $dependencies;

    /**
     *
     * @var \Chamilo\Libraries\Format\MessageLogger
     */
    protected $logger;

    /**
     *
     * @param \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency[] $dependencies
     */
    public function __construct($dependencies = [])
    {
        $this->set_dependencies($dependencies);

        $this->logger = MessageLogger::getInstance($this);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\MessageLogger
     */
    public function get_logger()
    {
        return $this->logger;
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency[] $dependencies
     */
    public function set_dependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency $dependency
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
        $html = [];

        foreach ($this->getDependencies() as $dependency)
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
        $messages = [];

        foreach ($this->getDependencies() as $dependency)
        {
            if ($dependency->check())
            {
                $success ++;
            }

            $messages[] = $dependency->get_logger()->render();
        }

        $result = ($success == count($this->getDependencies()));

        $operator = Translation::get('And');

        $this->get_logger()->add_message(implode(' ' . $operator . ' ', $messages));

        return $result;
    }

    public function needs($context)
    {
        foreach ($this->getDependencies() as $dependency)
        {
            if ($dependency->needs($context))
            {
                return true;
            }
        }

        return false;
    }
}
