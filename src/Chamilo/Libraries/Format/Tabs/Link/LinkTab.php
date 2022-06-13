<?php
namespace Chamilo\Libraries\Format\Tabs\Link;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Tabs\AbstractTab;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
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
        int $display = self::DISPLAY_ICON_AND_TITLE, $target = self::TARGET_WINDOW
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

    public function setConfirmationMessage(?string $confirmationMessage)
    {
        $this->confirmationMessage = $confirmationMessage;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link)
    {
        $this->link = $link;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position)
    {
        $this->position = $position;
    }

    public function getTarget(): int
    {
        return $this->target;
    }

    public function setTarget(int $target)
    {
        $this->target = $target;
    }

    public function hasConfirmationMessage(): bool
    {
        if ($this->getConfirmationMessage())
        {
            return true;
        }

        return false;
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }

    public function setIsSelected(bool $isSelected)
    {
        $this->isSelected = $isSelected;
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
