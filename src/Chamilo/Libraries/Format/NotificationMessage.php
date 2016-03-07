<?php
namespace Chamilo\Libraries\Format;

/**
 *
 * @package Chamilo\Libraries\Format
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NotificationMessage
{
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';

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
    public function __construct($message, $type = self :: TYPE_INFO)
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
        $html[] = '<div class="alert  alert-' . $this->type . ' . alert-dismissible" . role="alert">';
        $html[] = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        $html[] = $this->message;
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $type
     * @param string $message
     * @return string
     */
    public static function create($message, $type = self :: TYPE_INFO)
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
        return self :: create($message, self :: TYPE_SUCCESS);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function normal($message)
    {
        return self :: create($message, self :: TYPE_INFO);
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
        return self :: create($message, self :: TYPE_DANGER);
    }
}
