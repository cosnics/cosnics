<?php
namespace Chamilo\Libraries\Format\Tabs\Link;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Tabs\Tab;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkTab extends Tab
{
    public const POSITION_LEFT = 'left';
    public const POSITION_RIGHT = 'right';

    public const TARGET_POPUP = 2;
    public const TARGET_WINDOW = 1;

    protected int $display;

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
        $this->display = $display;
        $this->target = $target;
    }

    public function render(bool $isOnlyTab = false): string
    {
        return '';
    }

    public function getConfirmationMessage(): ?string
    {
        return $this->confirmationMessage;
    }

    public function setConfirmationMessage(?string $confirmationMessage)
    {
        $this->confirmationMessage = $confirmationMessage;
    }

    public function getDisplay(): int
    {
        return $this->display;
    }

    public function setDisplay(int $display)
    {
        $this->display = $display;
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

    public function header(): string
    {
        $classes = [];

        if ($this->isSelected())
        {
            $classes[] = 'active';
        }

        $classes[] = 'pull-' . $this->getPosition();

        $html = [];
        $html[] = '<li class="' . implode(' ', $classes) . '">';

        $link = [];
        $link[] = '<a';

        if ($this->getLink() && $this->getTarget() == self::TARGET_WINDOW)
        {
            $link[] = 'href="' . $this->getLink() . '"';

            if ($this->hasConfirmationMessage())
            {
                $link[] = 'onclick="return confirm(\'' . addslashes(
                        htmlentities(
                            $this->getConfirmationMessage() === true ? Translation::get(
                                'Confirm', null, StringUtilities::LIBRARIES
                            ) : $this->getConfirmationMessage()
                        )
                    ) . '\');"';
            }
        }
        elseif ($this->getLink() && $this->getTarget() == self::TARGET_POPUP)
        {
            $link[] = 'href="" onclick="javascript:openPopup(\'' . $this->getLink() . '\'); return false"';
        }
        else
        {
            $link[] = 'style="cursor: default;"';
        }

        $link[] = '>';

        $html[] = implode(' ', $link);

        if ($this->getInlineGlyph() && $this->isIconVisible())
        {
            $html[] = $this->getInlineGlyph()->render();
        }

        if ($this->getLabel() && $this->isTextVisible())
        {
            $html[] = '<span class="title">' . $this->getLabel() . '</span>';
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }

    public function setIsSelected(bool $isSelected)
    {
        $this->isSelected = $isSelected;
    }
}
