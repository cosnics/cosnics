<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

/**
 * Value object to describe a notification message
 *
 * @package Chamilo\Libraries\Format\NotificationMessage
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationMessage
{
    public const TYPE_DANGER = 'danger';
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_WARNING = 'warning';

    /**
     * A category for the message. Can be used to limit the number of messages of the same category.
     */
    protected ?string $category;

    protected string $message;

    protected string $type;

    public function __construct(string $message, string $type = self::TYPE_INFO, ?string $category = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->category = $category;
    }

    public static function confirm(string $message, ?string $category = null): NotificationMessage
    {
        return new self($message, self::TYPE_SUCCESS, $category);
    }

    public static function error(string $message, ?string $category = null): NotificationMessage
    {
        return new self($message, self::TYPE_DANGER, $category);
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public static function normal(string $message, ?string $category = null): NotificationMessage
    {
        return new self($message, self::TYPE_INFO, $category);
    }

    public static function warning(string $message, ?string $category = null): NotificationMessage
    {
        return new self($message, self::TYPE_WARNING, $category);
    }
}
