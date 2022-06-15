<?php
namespace Chamilo\Core\Repository\Selector;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * An option in a TypeSelector
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TypeSelectorOption extends TypeSelectorItemInterface
{

    public function get_image_path(): ?InlineGlyph;

    /**
     *
     * @return string
     */
    public function get_label(): ?string;
}