<?php
namespace Chamilo\Libraries\Storage\Exception;

use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Storage\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassNoResultException extends \Exception
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
     * @var \Chamilo\Libraries\Storage\\Parameters\DataClassResultParameters
     */
    private $parameters;

    /**
     * Constructor
     * 
     * @param string $class_name
     * @param \Chamilo\Libraries\Storage\\Parameters\DataClassRetrieveParameters $parameters
     * @param string $message
     * @param integer $code
     * @param \Exception $previous
     */
    public function __construct($class_name, $parameters, $message = null, $code = null, $previous = null)
    {
        $this->class_name = $class_name;
        $this->parameters = $parameters;
        
        $message = Translation::get(
            'DataClassNoResultException', 
            array('CLASS_NAME' => $class_name, 'MESSAGE' => $message));
        
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
     * @param $class_name string
     */
    public function set_class_name($class_name)
    {
        $this->class_name = $class_name;
    }

    /**
     * Get the parameters of the DataClass object retrieval for which no result was found
     * 
     * @return \Chamilo\Libraries\Storage\\Parameters\DataClassRetrieveParameters
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    /**
     * Set the parameters of the DataClass object retrieval for which no result was found
     * 
     * @param \Chamilo\Libraries\Storage\\Parameters\DataClassRetrieveParameters $parameters
     */
    public function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
