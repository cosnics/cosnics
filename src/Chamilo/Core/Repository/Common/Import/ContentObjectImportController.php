<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib
 */
abstract class ContentObjectImportController
{

    private $messages;
    const TYPE_ERROR = 1;
    const TYPE_WARNING = 2;
    const TYPE_CONFIRM = 3;
    const TYPE_NORMAL = 4;

    /**
     *
     * @var ImportParameters $parameters
     */
    private $parameters;

    /**
     *
     * @param $parameters ImportParameters
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     *
     * @return ImportParameters
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    /**
     *
     * @param $type string
     * @param $parameters ImportParameters
     * @return ContentObjectImportController
     */
    public static function factory($parameters)
    {
        $class = __NAMESPACE__ . '\\' .
             (string) StringUtilities::getInstance()->createString($parameters->get_type())->upperCamelize() . '\\' .
             (string) StringUtilities::getInstance()->createString($parameters->get_type())->upperCamelize() .
             'ContentObjectImportController';
        return new $class($parameters);
    }

    /**
     *
     * @return boolean integer[]
     */
    abstract public function run();

    /**
     * Adds a message to the message list
     * 
     * @param String $message
     * @param int $type
     */
    public function add_message($message, $type)
    {
        if (! isset($this->messages[$type]))
        {
            $this->messages[$type] = [];
        }
        
        $this->messages[$type][] = $message;
    }

    /**
     * Checks wether the object has messages
     * 
     * @return booleans
     */
    public function has_messages($type)
    {
        return count($this->get_messages($type)) > 0;
    }

    /**
     * Retrieves the list of messages
     * 
     * @return Array
     */
    public function get_messages($type = null)
    {
        if ($type)
        {
            return isset($this->messages[$type]) ? $this->messages[$type] : [];
        }
        else
        {
            return $this->messages;
        }
    }

    /**
     * Clears the errors
     */
    public function clear_messages($type)
    {
        unset($this->messages[$type]);
    }

    public function get_messages_for_url()
    {
        $messages = [];
        $message_types = [];
        
        foreach ($this->get_messages() as $type => $type_messages)
        {
            foreach ($type_messages as $message)
            {
                $messages[] = $message;
                $message_types[] = $type;
            }
        }
        
        return array(Application::PARAM_MESSAGE => $messages, Application::PARAM_MESSAGE_TYPE => $message_types);
    }

    public static function get_allowed_extensions()
    {
        return [];
    }
}
