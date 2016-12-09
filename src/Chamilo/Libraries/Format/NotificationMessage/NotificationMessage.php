<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

/**
 * Value object to describe a notification message
 * 
 * @package Chamilo\Libraries\Format
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
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
    protected $type;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     * A category for the message.
     * Can be used to limit the number of messages of the same category.
     * 
     * @var string
     */
    protected $category;

    /**
     *
     * @param string $message
     * @param string $type
     * @param null $category
     */
    public function __construct($message, $type = self :: TYPE_INFO, $category = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->category = $category;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     *
     * @param string $message
     * @param string $category
     *
     * @return string
     */
    public static function confirm($message, $category = null)
    {
        return new self($message, self::TYPE_SUCCESS, $category);
    }

    /**
     *
     * @param string $message
     * @param string $category
     *
     * @return string
     */
    public static function normal($message, $category = null)
    {
        return new self($message, self::TYPE_INFO, $category);
    }

    /**
     *
     * @param string $message
     * @param string $category
     *
     * @return string
     */
    public static function warning($message, $category = null)
    {
        return new self($message, self::TYPE_WARNING, $category);
    }

    /**
     *
     * @param string $message
     * @param string $category
     *
     * @return string
     */
    public static function error($message, $category = null)
    {
        return new self($message, self::TYPE_DANGER, $category);
    }
}
