<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 */
class Breadcrumb
{

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $image;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    private $inlineGlyph;

    /**
     *
     * @param string $url
     * @param string $name
     * @param string $image
     * @param \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $inlineGlyph
     */
    public function __construct($url, $name, $image = null, InlineGlyph $inlineGlyph = null)
    {
        $this->url = $url;
        $this->name = $name;
        $this->image = $image;
        $this->inlineGlyph = $inlineGlyph;
    }

    /**
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    public function getInlineGlyph()
    {
        return $this->inlineGlyph;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $inlineGlyph
     */
    public function setInlineGlyph(InlineGlyph $inlineGlyph = null)
    {
        $this->inlineGlyph = $inlineGlyph;
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     *
     * @param string $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }
}
