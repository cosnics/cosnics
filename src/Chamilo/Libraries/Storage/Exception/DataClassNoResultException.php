<?php
namespace Chamilo\Libraries\Storage\Exception;

use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassNoResultException extends Exception
{

    /**
     * The fully qualified class name of the DataClass object retrieval for which no result was found
     *
     * @var string
     */
    private $class_name;

    /**
     * The parameters of the DataClass object retrieval for which no result was found
     *
     * @var \Chamilo\Libraries\Storage\Parameters\DataClassParameters
     */
    private $parameters;

    /**
     * Constructor
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @param string $message
     * @param integer $code
     * @param \Exception $previous
     */
    public function __construct($className, $parameters, $message = null, $code = null, $previous = null)
    {
        $this->class_name = $className;
        $this->parameters = $parameters;

        $message = Translation::get(
            'DataClassNoResultException', array('CLASS_NAME' => $className, 'MESSAGE' => $message)
        );

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the fully qualified class name of the DataClass object retrieval for which no result was found
     *
     * @return string
     */
    public function get_class_name()
    {
        return $this->class_name;
    }

    /**
     * Set the fully qualified class name of the DataClass object retrieval for which no result was found
     *
     * @param $className string
     */
    public function set_class_name($className)
    {
        $this->class_name = $className;
    }

    /**
     * Get the parameters of the DataClass object retrieval for which no result was found
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassParameters
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    /**
     * Set the parameters of the DataClass object retrieval for which no result was found
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     */
    public function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
