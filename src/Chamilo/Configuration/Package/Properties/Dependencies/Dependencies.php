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
    const PROPERTY_OPERATOR = 'operator';
    const PROPERTY_DEPENDENCIES = 'dependencies';
    const OPERATOR_AND = 1;
    const OPERATOR_OR = 2;

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
    public function __construct($operator, $dependencies = array())
    {
        $this->set_operator($operator);
        $this->set_dependencies($dependencies);
        
        $this->logger = MessageLogger :: get_instance($this);
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
     * @return int
     */
    public function get_operator()
    {
        return $this->operator;
    }

    /**
     *
     * @param int $operator
     */
    public function set_operator($operator)
    {
        $this->operator = $operator;
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
        
        if ($this->get_operator() == self :: OPERATOR_AND)
        {
            $operator = Translation :: get('And');
        }
        elseif ($this->get_operator() == self :: OPERATOR_OR)
        {
            $operator = Translation :: get('Or');
        }
        else
        {
            $operator = '?';
        }
        
        return implode(' ' . $operator . ' ', $html);
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
        
        if ($this->get_operator() == self :: OPERATOR_AND)
        {
            $result = ($success == count($this->get_dependencies()));
        }
        elseif ($this->get_operator() == self :: OPERATOR_OR)
        {
            $result = ($success > 0);
        }
        else
        {
            $result = false;
        }
        
        // if (! $result)
        // {
        if ($this->get_operator() == self :: OPERATOR_AND)
        {
            $operator = Translation :: get('And');
        }
        elseif ($this->get_operator() == self :: OPERATOR_OR)
        {
            $operator = Translation :: get('Or');
        }
        else
        {
            $operator = '?';
        }
        
        $this->get_logger()->add_message(implode(' ' . $operator . ' ', $messages));
        // }
        
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
