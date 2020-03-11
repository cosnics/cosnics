<?php
namespace Chamilo\Core\Repository\Selector\Option;

use Chamilo\Core\Repository\Selector\TypeSelectorOption;
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
     * @var \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    private $glyph;

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

    public function get_image_path()
    {
        return $this->glyph;
    }

    public function get_label()
    {
        return Translation::get($this->get_type(), null, $this->get_context());
    }

    /*
     * (non-PHPdoc) @see \core\repository\TypeSelectorOption::get_image_path()
     */

    /**
     *
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /*
     * (non-PHPdoc) @see \core\repository\TypeSelectorOption::get_label()
     */

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }
}