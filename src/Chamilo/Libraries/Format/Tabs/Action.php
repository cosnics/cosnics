<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
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

    public function render(): string
    {
        $html = [];

        if ($this->hasConfirmationMessage())
        {
            $onclick = 'onclick = "return confirm(\'' . $this->getConfirmationMessage() . '\')"';
        }
        else
        {
            $onclick = '';
        }

        $html[] = '<div class="list-group-item vertical-action">';

        $html[] = '<div class="pull-left icon">';
        $html[] = '<a href="' . $this->getUrl() . '" ' . $onclick . '>';

        $html[] = $this->getInlineGlyph()->render();

        $html[] = '</a>';
        $html[] = '</div>';

        $html[] = '<div class="pull-left">';

        if ($this->getTitle())
        {
            $html[] = '<h5 class="list-group-item-heading"><a href="' . $this->getUrl() . '" ' . $onclick . '>' .
                $this->getTitle() . '</a></h5>';
        }

        $html[] = '<p class="list-group-item-text">' . $this->getContent() . '</p>';
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getConfirmationMessage(): bool
    {
        return $this->confirmationMessage;
    }

    public function setConfirmationMessage(bool $confirmationMessage)
    {
        $this->confirmationMessage = $confirmationMessage;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function getInlineGlyph(): ?InlineGlyph
    {
        return $this->inlineGlyph;
    }

    public function setInlineGlyph(?InlineGlyph $inlineGlyph)
    {
        $this->inlineGlyph = $inlineGlyph;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return bool
     */
    public function hasConfirmationMessage(): bool
    {
        return !empty($this->confirmationMessage);
    }
}
