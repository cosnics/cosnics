<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Domain;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Action
{

    private ?string $confirmationMessage;

    private string $content;

    private ?InlineGlyph $inlineGlyph;

    private ?string $title;

    private ?string $url;

    public function __construct(
        string $content, ?string $title = null, ?InlineGlyph $inlineGlyph = null, ?string $url = null,
        ?string $confirmationMessage = null
    )
    {
        $this->title = $title;
        $this->content = $content;
        $this->inlineGlyph = $inlineGlyph;
        $this->url = $url;
        $this->confirmationMessage = $confirmationMessage;
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getInlineGlyph(): ?InlineGlyph
    {
        return $this->inlineGlyph;
    }

    public function setInlineGlyph(?InlineGlyph $inlineGlyph): static
    {
        $this->inlineGlyph = $inlineGlyph;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function hasConfirmationMessage(): bool
    {
        return !empty($this->confirmationMessage);
    }
}
