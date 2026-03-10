<?php
namespace Chamilo\Libraries\UserInterface\Alert\Architecture\Domain;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;

/**
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Alert
{
    /**
     * A category for the message. Can be used to limit the number of messages of the same category.
     */
    protected ?string $category;

    protected string $message;

    protected AlertEnum $type;

    public function __construct(string $message, AlertEnum $type = AlertEnum::INFO, ?string $category = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->category = $category;
    }

    public static function confirm(string $message, ?string $category = null): Alert
    {
        return new self($message, AlertEnum::SUCCESS, $category);
    }

    public static function error(string $message, ?string $category = null): Alert
    {
        return new self($message, AlertEnum::DANGER, $category);
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): AlertEnum
    {
        return $this->type;
    }

    public static function normal(string $message, ?string $category = null): Alert
    {
        return new self($message, AlertEnum::INFO, $category);
    }

    public static function warning(string $message, ?string $category = null): Alert
    {
        return new self($message, AlertEnum::WARNING, $category);
    }
}
