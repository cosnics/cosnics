<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Domain;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkTab extends AbstractTab
{
    public const POSITION_LEFT = 'left';
    public const POSITION_RIGHT = 'right';
    public const TARGET_POPUP = 2;
    public const TARGET_WINDOW = 1;

    private ?string $confirmationMessage;

    private bool $isSelected;

    private string $link;

    private string $position;

    private int $target;

    public function __construct(
        string $identifier, string $label, ?InlineGlyph $inlineGlyph, string $link, bool $isSelected = false,
        ?string $confirmationMessage = null, string $position = self::POSITION_LEFT,
        DisplayTypeEnum $display = DisplayTypeEnum::ICON_AND_LABEL, $target = self::TARGET_WINDOW
    )
    {
        parent::__construct($identifier, $label, $inlineGlyph, $display);
        $this->link = $link;
        $this->isSelected = $isSelected;
        $this->confirmationMessage = $confirmationMessage;
        $this->position = $position;
        $this->target = $target;
    }

    public function getConfirmationMessage(): ?string
    {
        return $this->confirmationMessage;
    }

    public function setConfirmationMessage(?string $confirmationMessage): static
    {
        $this->confirmationMessage = $confirmationMessage;

        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getTarget(): int
    {
        return $this->target;
    }

    public function setTarget(int $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function hasConfirmationMessage(): bool
    {
        if ($this->getConfirmationMessage()) {
            return true;
        }

        return false;
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }

    public function setIsSelected(bool $isSelected): static
    {
        $this->isSelected = $isSelected;

        return $this;
    }

    public function opensInPopup(): bool
    {
        return $this->getTarget() == self::TARGET_POPUP;
    }

    public function opensInWindow(): bool
    {
        return $this->getTarget() == self::TARGET_WINDOW;
    }
}
