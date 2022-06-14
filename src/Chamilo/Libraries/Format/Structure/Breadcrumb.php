<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 */
class Breadcrumb
{

    private ?InlineGlyph $inlineGlyph;

    private string $name;

    private ?string $url;

    public function __construct(?string $url = null, string $name, ?InlineGlyph $inlineGlyph = null)
    {
        $this->url = $url;
        $this->name = $name;
        $this->inlineGlyph = $inlineGlyph;
    }

    public function getInlineGlyph(): ?InlineGlyph
    {
        return $this->inlineGlyph;
    }

    public function setInlineGlyph(?InlineGlyph $inlineGlyph)
    {
        $this->inlineGlyph = $inlineGlyph;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @deprecated Use Breadcrumb::getUrl() now
     */
    public function get_url(): ?string
    {
        return $this->getUrl();
    }

    public function setUrl(?string $url)
    {
        $this->url = $url;
    }

    /**
     * @deprecated Use Breadcrumb::setUrl() now
     */
    public function set_url(?string $url)
    {
        $this->setUrl($url);
    }

    /**
     * @deprecated Use Breadcrumb::getName() now
     */
    public function get_name(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @deprecated Use Breadcrumb::setName() now
     */
    public function set_name(string $name)
    {
        $this->setName($name);
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}
