<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Libraries\Format\Structure
 */
class ToolbarItem
{
    public const DISPLAY_ICON = 1;
    public const DISPLAY_ICON_AND_LABEL = 3;
    public const DISPLAY_LABEL = 2;

    private ?string $class;

    private bool $confirmation;

    private ?string $confirmationMessage;

    private int $display;

    /**
     * @var string[]
     */
    private array $extraAttributes;

    private ?string $href;

    private ?InlineGlyph $image;

    private ?string $label;

    private ?string $target;

    public function __construct(
        ?string $label = null, ?InlineGlyph $image = null, ?string $href = null,
        int $display = self::DISPLAY_ICON_AND_LABEL, bool $confirmation = false, ?string $class = null,
        ?string $target = null, ?string $confirmationMessage = null, array $extraAttributes = []
    )
    {
        $this->label = $label;
        $this->display = $display;
        $this->image = $image;
        $this->href = $href;
        $this->confirmation = $confirmation;
        $this->class = $class;
        $this->target = $target;
        $this->confirmationMessage = $confirmationMessage;
        $this->extraAttributes = $extraAttributes;
    }

    public function render(): string
    {
        $buttonRenderer = new ButtonRenderer($this->convertToButton());

        return $buttonRenderer->render();
    }

    public function convertToButton(bool $keepDisplayProperty = true): Button
    {
        $label = ($this->getLabel() ? htmlspecialchars($this->getLabel()) : null);

        if ($keepDisplayProperty)
        {
            $display = !$this->getDisplay() ? self::DISPLAY_ICON : $this->getDisplay();
        }
        else
        {
            $display = AbstractButton::DISPLAY_ICON_AND_LABEL;
        }

        $elementClasses = !empty($this->class) ? explode(' ', $this->class) : [];
        array_unshift($elementClasses, 'btn-link');

        $confirmation = $this->getConfirmation();
        $confirmationMessage = $this->getConfirmationMessage();

        if ($confirmation === true && is_string($confirmationMessage) && !empty($confirmationMessage))
        {
            $buttonConfirmationMessage = $confirmationMessage;
        }
        else
        {
            $buttonConfirmationMessage = null;
        }

        return new Button(
            $label, $this->getImage(), $this->getHref(), $display, $buttonConfirmationMessage, $elementClasses,
            $this->getTarget()
        );
    }

    public function getClasses(): ?string
    {
        return $this->class;
    }

    public function getConfirmation(): bool
    {
        return $this->confirmation;
    }

    public function setConfirmation(bool $confirmation): static
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    public function getConfirmationMessage(): ?string
    {
        return $this->confirmationMessage;
    }

    public function setConfirmationMessage(?string $message): static
    {
        $this->confirmationMessage = $message;

        return $this;
    }

    public function getDisplay(): int
    {
        return $this->display;
    }

    public function setDisplay(int $display): static
    {
        $this->display = $display;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    /**
     * @param ?string[] $extraAttributes
     */
    public function setExtraAttributes(array $extraAttributes = []): static
    {
        $this->extraAttributes = $extraAttributes;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(?string $href): static
    {
        $this->href = $href;

        return $this;
    }

    public function getImage(): ?InlineGlyph
    {
        return $this->image;
    }

    public function setImage(?InlineGlyph $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function needsConfirmation(): bool
    {
        return !($this->getConfirmation() === false);
    }
}
