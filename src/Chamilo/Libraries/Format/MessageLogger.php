<?php
namespace Chamilo\Libraries\Format;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Libraries\Format
 */
class MessageLogger
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';

    /**
     *
     * @var \Chamilo\Libraries\Format\MessageLogger[]
     */
    private static $instances;

    /**
     *
     * @var string[]
     */
    private $messages;

    /**
     *
     * @param string $instanceName
     * @return \Chamilo\Libraries\Format\MessageLogger
     */
    public static function get_instance_by_name($instanceName)
    {
        if (! isset(self::$instances[$instanceName]))
        {
            self::$instances[$instanceName] = new MessageLogger();
        }

        return self::$instances[$instanceName];
    }

    /**
     *
     * @param \stdClass $object
     * @return \Chamilo\Libraries\Format\MessageLogger
     */
    public static function getInstance($object)
    {
        return self::get_instance_by_name(ClassnameUtilities::getInstance()->getClassnameFromObject($object, true));
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\MessageLogger[]
     */
    public static function get_instances()
    {
        return self::$instances;
    }

    public function __construct()
    {
        $this->messages = array();
    }

    /**
     *
     * @param string $message
     * @param integer $type
     */
    public function add_message($message, $type = self::TYPE_NORMAL)
    {
        switch ($type)
        {
            case self::TYPE_NORMAL :
                $this->messages[] = $message;
                break;
            case self::TYPE_CONFIRM :
                $this->messages[] = '<span style="color: green; font-weight: bold;">' . $message . '</span>';
                break;
            case self::TYPE_WARNING :
                $this->messages[] = '<span style="color: orange; font-weight: bold;">' . $message . '</span>';
                break;
            case self::TYPE_ERROR :
                $this->messages[] = '<span style="color: red; font-weight: bold;">' . $message . '</span>';
                break;
            default :
                $this->messages[] = $message;
                break;
        }
    }

    /**
     *
     * @param string[] $messages
     */
    public function set_messages($messages)
    {
        $this->messages = $messages;
    }

    /**
     *
     * @return string[]
     */
    public function get_messages()
    {
        return $this->messages;
    }

    public function truncate()
    {
        $this->set_messages(array());
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $message = implode('<br />' . "\n", $this->get_messages());
        $this->truncate();
        return $message;
    }

    /**
     *
     * @return string
     */
    public function render_for_cli()
    {
        $message = strip_tags(implode(PHP_EOL, $this->get_messages()));
        $this->truncate();
        return $message;
    }
}
