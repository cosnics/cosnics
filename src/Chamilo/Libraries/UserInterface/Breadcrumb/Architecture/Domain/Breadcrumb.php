<?php
namespace Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Breadcrumb
{
    private ?InlineGlyph $inlineGlyph;

    private string $name;

    private ?string $url;

    public function __construct(string $name, ?string $url = null, ?InlineGlyph $inlineGlyph = null)
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
