<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib
 */
abstract class ContentObjectImportController
{
    use DependencyInjectionContainerTrait;

    public const TYPE_CONFIRM = 3;
    public const TYPE_ERROR = 1;
    public const TYPE_NORMAL = 4;
    public const TYPE_WARNING = 2;

    private $messages;

    /**
     * @var ImportParameters $parameters
     */
    private $parameters;

    /**
     * @param $parameters ImportParameters
     *
     * @throws \Exception
     */
    public function __construct($parameters)
    {
        $this->initializeContainer();

        $this->parameters = $parameters;
    }

    /**
     * @return bool integer[]
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
        if (!isset($this->messages[$type]))
        {
            $this->messages[$type] = [];
        }

        $this->messages[$type][] = $message;
    }

    /**
     * Clears the errors
     */
    public function clear_messages($type)
    {
        unset($this->messages[$type]);
    }

    /**
     * @param $type       string
     * @param $parameters ImportParameters
     *
     * @return ContentObjectImportController
     */
    public static function factory($parameters)
    {
        $class = __NAMESPACE__ . '\\' .
            (string) StringUtilities::getInstance()->createString($parameters->getType())->upperCamelize() . '\\' .
            (string) StringUtilities::getInstance()->createString($parameters->getType())->upperCamelize() .
            'ContentObjectImportController';

        return new $class($parameters);
    }

    protected function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->getService(ContentObjectRelationService::class);
    }

    public static function get_allowed_extensions()
    {
        return [];
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

        return [Application::PARAM_MESSAGE => $messages, Application::PARAM_MESSAGE_TYPE => $message_types];
    }

    /**
     * @return ImportParameters
     */
    public function get_parameters()
    {
        return $this->parameters;
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
}
