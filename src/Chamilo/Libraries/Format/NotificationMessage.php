<?php
namespace Chamilo\Libraries\Format;

/**
 *
 * @author Hans De Bisschop
 */
class NotificationMessage
{
    const TYPE_ERROR = 1;
    const TYPE_WARNING = 2;
    const TYPE_CONFIRM = 3;
    const TYPE_NORMAL = 4;

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var string
     */
    private $message;

    /**
     *
     * @param string $type
     * @param string $message
     */
    public function __construct($message, $type = self :: TYPE_NORMAL)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     *
     * @return the $type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return the $message
     */
    public function get_message()
    {
        return $this->message;
    }

    /**
     *
     * @param string $message
     */
    public function set_message($message)
    {
        $this->message = $message;
    }

    /**
     *
     * @return string
     */
    public function to_html()
    {
        $html = array();
        $html[] = '<div class="notification notification-' . $this->type . '">';
        $html[] = $this->message;
        $html[] = '<div class="close_message" id="closeMessage"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $type
     * @param string $message
     * @return string
     */
    public static function create($message, $type = self :: TYPE_NORMAL)
    {
        return new self($message, $type);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function confirm($message)
    {
        return self :: create($message, self :: TYPE_CONFIRM);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function normal($message)
    {
        return self :: create($message, self :: TYPE_NORMAL);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function warning($message)
    {
        return self :: create($message, self :: TYPE_WARNING);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function error($message)
    {
        return self :: create($message, self :: TYPE_ERROR);
    }
}
