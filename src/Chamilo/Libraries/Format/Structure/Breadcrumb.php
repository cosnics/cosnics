<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Breadcrumb
{

    private ?InlineGlyph $inlineGlyph;

    private string $name;

    private string $url;

    public function __construct(string $url, string $name, ?InlineGlyph $inlineGlyph = null)
    {
        $this->url = $url;
        $this->name = $name;
        $this->inlineGlyph = $inlineGlyph;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
