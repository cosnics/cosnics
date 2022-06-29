<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Structure
 */
class ToolbarItem
{
    public const DISPLAY_ICON = 1;
    public const DISPLAY_ICON_AND_LABEL = 3;
    public const DISPLAY_LABEL = 2;

    private ?string $class;

    /**
     * @var bool|string
     */
    private $confirmation;

    private ?string $confirmationMessage;

    private int $display;

    /**
     * @var string[]
     */
    private ?array $extraAttributes;

    private ?string $href;

    private ?InlineGlyph $image;

    private ?string $label;

    private ?string $target;

    public function __construct(
        ?string $label = null, ?InlineGlyph $image = null, ?string $href = null,
        int $display = self::DISPLAY_ICON_AND_LABEL, $confirmation = false, ?string $class = null,
        ?string $target = null, ?string $confirmationMessage = null, ?array $extraAttributes = null
    )
    {
        $this->label = $label;
        $this->display = $display;
        $this->image = $image;
        $this->href = $href;
        $this->confirmation = $confirmation;
        $this->class = $class;
        $this->target = $target;

        if ($confirmationMessage == null)
        {
            $this->confirmationMessage = Translation::get('Confirm', null, StringUtilities::LIBRARIES);
        }
        else
        {
            $this->confirmationMessage = $confirmationMessage;
        }

        $this->extraAttributes = $extraAttributes;
    }

    public function render(): string
    {
        $buttonRenderer = new ButtonRenderer($this->convertToButton());

        return $buttonRenderer->render();
    }

    /**
     * @deprecated Use ToolbarItem::render() now
     */
    public function as_html(): string
    {
        return $this->render();
    }

    public function convertToButton(bool $keepDisplayProperty = true): Button
    {
        $label = ($this->get_label() ? htmlspecialchars($this->get_label()) : null);

        if($keepDisplayProperty)
        {
            $display = !$this->get_display() ? self::DISPLAY_ICON : $this->get_display();
        }
        else
        {
            $display = AbstractButton::DISPLAY_ICON_AND_LABEL;
        }

        $elementClasses = !empty($this->class) ? explode(' ', $this->class) : [];
        array_unshift($elementClasses, 'btn-link');

        $confirmation = $this->get_confirmation();
        $confirmationMessage = $this->get_confirm_message();

        if (is_string($confirmation))
        {
            $buttonConfirmationMessage = $confirmation;
        }
        elseif ($confirmation === true && is_string($confirmationMessage) && !empty($confirmationMessage))
        {
            $buttonConfirmationMessage = $confirmationMessage;
        }
        elseif ($confirmation === true)
        {
            $buttonConfirmationMessage = Translation::get('ConfirmChosenAction', [], StringUtilities::LIBRARIES);
        }
        else
        {
            $buttonConfirmationMessage = null;
        }

        return new Button(
            $label, $this->get_image(), $this->get_href(), $display, $buttonConfirmationMessage, $elementClasses,
            $this->get_target()
        );
    }

    public function getClasses(): ?string
    {
        return $this->class;
    }

    /**
     * @return ?string[]
     */
    public function getExtraAttributes(): ?array
    {
        return $this->extraAttributes;
    }

    /**
     * @param ?string[] $extraAttributes
     */
    public function setExtraAttributes(?array $extraAttributes)
    {
        $this->extraAttributes = $extraAttributes;
    }

    public function get_confirm_message(): ?string
    {
        return $this->confirmationMessage;
    }

    /**
     * @return bool|string
     */
    public function get_confirmation()
    {
        return $this->confirmation;
    }

    /**
     * @param bool|string $confirmation
     */
    public function set_confirmation($confirmation)
    {
        $this->confirmation = $confirmation;
    }

    public function get_display(): int
    {
        return $this->display;
    }

    public function set_display(int $display)
    {
        $this->display = $display;
    }

    public function get_href(): ?string
    {
        return $this->href;
    }

    public function set_href(?string $href)
    {
        $this->href = $href;
    }

    public function get_image(): ?InlineGlyph
    {
        return $this->image;
    }

    public function set_image(?InlineGlyph $image)
    {
        $this->image = $image;
    }

    public function get_label(): ?string
    {
        return $this->label;
    }

    public function set_label(?string $label)
    {
        $this->label = $label;
    }

    public function get_target(): ?string
    {
        return $this->target;
    }

    public function set_target(?string $target)
    {
        $this->target = $target;
    }

    public function needsConfirmation(): bool
    {
        if ($this->get_confirmation() === false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function set_confirm_message(?string $message)
    {
        $this->confirmationMessage = $message;
    }
}
