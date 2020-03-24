<?php
namespace Chamilo\Libraries\Format;

use InvalidArgumentException;

/**
 *
 * @package Chamilo\Libraries\Format
 * @author Roan Embrechts
 * @author Tim De Pauw
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Display
{
    const MESSAGE_TYPE_CONFIRM = 'confirm';
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_FATAL = 'fatal';
    const MESSAGE_TYPE_NORMAL = 'normal';
    const MESSAGE_TYPE_WARNING = 'warning';

    /**
     *
     * @param string $message
     *
     * @return string
     */
    public static function error_message($message)
    {
        return self::message(self::MESSAGE_TYPE_ERROR, $message);
    }

    /**
     *
     * @param string $type
     * @param string $message
     *
     * @return string
     */
    public static function message($type = self::MESSAGE_TYPE_NORMAL, $message)
    {
        $html = array();

        switch ($type)
        {
            case self::MESSAGE_TYPE_CONFIRM:
                $class = 'success';
                break;
            case self::MESSAGE_TYPE_NORMAL:
                $class = 'info';
                break;
            case self::MESSAGE_TYPE_ERROR:
            case self::MESSAGE_TYPE_FATAL:
                $class = 'danger';
                break;
            case self::MESSAGE_TYPE_WARNING:
                $class = 'warning';
                break;
            default:
                throw new InvalidArgumentException();
        }

        $html[] = '<div class="alert alert-cosnics alert-' . $class . ' alert-dismissible">';
        $html[] = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        $html[] = '<span aria-hidden="true">&times;</span>';
        $html[] = '</button>';
        $html[] = $message;
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $message
     *
     * @return string
     */
    public static function normal_message($message)
    {
        return self::message(self::MESSAGE_TYPE_NORMAL, $message);
    }

    /**
     *
     * @param string $message
     *
     * @return string
     */
    public static function warning_message($message)
    {
        return self::message(self::MESSAGE_TYPE_WARNING, $message);
    }
}
