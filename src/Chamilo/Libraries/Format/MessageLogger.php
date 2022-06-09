<?php
namespace Chamilo\Libraries\Format;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Libraries\Format
 */
class MessageLogger
{
    public const TYPE_CONFIRM = 2;
    public const TYPE_ERROR = 4;
    public const TYPE_NORMAL = 1;
    public const TYPE_WARNING = 3;

    /**
     * @var \Chamilo\Libraries\Format\MessageLogger[]
     */
    private static array $instances = [];

    /**
     * @var string[]
     */
    private array $messages;

    public function __construct()
    {
        $this->messages = [];
    }

    public function render(): string
    {
        $message = implode('<br />' . PHP_EOL, $this->get_messages());
        $this->truncate();

        return $message;
    }

    public function add_message(string $message, int $type = self::TYPE_NORMAL)
    {
        switch ($type)
        {
            case self::TYPE_CONFIRM :
                $this->messages[] = '<span class="text-success"><strong>' . $message . '</strong></span>';
                break;
            case self::TYPE_WARNING :
                $this->messages[] = '<span class="text-warning"><strong>' . $message . '</strong></span>';
                break;
            case self::TYPE_ERROR :
                $this->messages[] = '<span class="text-danger"><strong>' . $message . '</strong></span>';
                break;
            case self::TYPE_NORMAL :
            default :
                $this->messages[] = $message;
                break;
        }
    }

    /**
     * @throws \ReflectionException
     */
    public static function getInstance(object $object): MessageLogger
    {
        return self::get_instance_by_name(ClassnameUtilities::getInstance()->getClassnameFromObject($object, true));
    }

    public static function get_instance_by_name(string $instanceName): MessageLogger
    {
        if (!isset(self::$instances[$instanceName]))
        {
            self::$instances[$instanceName] = new MessageLogger();
        }

        return self::$instances[$instanceName];
    }

    /**
     * @return string[]
     */
    public function get_messages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     */
    public function set_messages(array $messages)
    {
        $this->messages = $messages;
    }

    public function truncate()
    {
        $this->set_messages([]);
    }
}
