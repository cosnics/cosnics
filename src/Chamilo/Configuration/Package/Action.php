<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Translation\Translation;

abstract class Action
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    
    // Types
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';

    private $message;

    public function __construct()
    {
        $this->message = array();
    }

    public function add_message($type = self :: TYPE_NORMAL, $message)
    {
        switch ($type)
        {
            case self::TYPE_NORMAL :
                $this->message[] = $message;
                break;
            case self::TYPE_CONFIRM :
                $this->message[] = '<span style="color: green; font-weight: bold;">' . $message . '</span>';
                break;
            case self::TYPE_WARNING :
                $this->message[] = '<span style="color: orange; font-weight: bold;">' . $message . '</span>';
                break;
            case self::TYPE_ERROR :
                $this->message[] = '<span style="color: red; font-weight: bold;">' . $message . '</span>';
                break;
            default :
                $this->message[] = $message;
                break;
        }
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    /**
     *
     * @return multitype:string
     */
    public function get_message()
    {
        return $this->message;
    }

    public function failed($error_message)
    {
        $this->add_message(self::TYPE_ERROR, $error_message);
        $this->add_message(self::TYPE_ERROR, Translation::get($this->get_type() . 'Failed'));
        return false;
    }

    public function successful()
    {
        $this->add_message(self::TYPE_CONFIRM, Translation::get($this->get_type() . 'Successful'));
        return true;
    }

    /**
     *
     * @return string
     */
    public function retrieve_message()
    {
        return implode('<br />' . PHP_EOL, $this->get_message());
    }

    public function get_type()
    {
        return ClassnameUtilities::getInstance()->getClassnameFromObject($this);
    }

    public function get_path()
    {
        return Path::getInstance()->namespaceToFullPath(static::package());
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context());
    }
}
