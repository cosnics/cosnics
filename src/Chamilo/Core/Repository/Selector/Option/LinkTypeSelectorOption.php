<?php
namespace Chamilo\Core\Repository\Selector\Option;

use Chamilo\Core\Repository\Selector\TypeSelectorOption;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkTypeSelectorOption implements TypeSelectorOption
{

    /**
     *
     * @var string
     */
    private $context;

    /**
     * @var \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    private $glyph;

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @param string $context
     * @param string $type
     * @param string $label
     * @param \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $glyph
     */
    public function __construct($context, $type, $url, $glyph)
    {
        $this->context = $context;
        $this->type = $type;
        $this->url = $url;
        $this->glyph = $glyph;
    }

    /**
     *
     * @return string
     */
    public function get_context()
    {
        return $this->context;
    }

    public function get_image_path(): ?InlineGlyph
    {
        return $this->glyph;
    }

    public function get_label(): ?string
    {
        return Translation::get($this->getType(), null, $this->get_context());
    }

    /**
     *
     * @deprecated Use LinkTypeSelectorOption::getType()
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }
}