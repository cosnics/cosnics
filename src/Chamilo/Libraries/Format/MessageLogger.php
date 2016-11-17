<?php
namespace Chamilo\Libraries\Format;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

class MessageLogger
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';

    private static $instances;

    private $messages;

    public static function get_instance_by_name($instance_name)
    {
        if (! isset(self::$instances[$instance_name]))
        {
            self::$instances[$instance_name] = new MessageLogger();
        }
        
        return self::$instances[$instance_name];
    }

    public static function getInstance($object)
    {
        return self::get_instance_by_name(ClassnameUtilities::getInstance()->getClassnameFromObject($object, true));
    }

    public static function get_instances()
    {
        return self::$instances;
    }

    public function __construct()
    {
        $this->messages = array();
    }

    public function add_message($message, $type = self :: TYPE_NORMAL)
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

    public function set_messages($messages)
    {
        $this->messages = $messages;
    }

    public function get_messages()
    {
        return $this->messages;
    }

    public function truncate()
    {
        $this->set_messages(array());
    }

    public function render()
    {
        $message = implode('<br />' . "\n", $this->get_messages());
        $this->truncate();
        return $message;
    }

    public function render_for_cli()
    {
        $message = strip_tags(implode(PHP_EOL, $this->get_messages()));
        $this->truncate();
        return $message;
    }
}
