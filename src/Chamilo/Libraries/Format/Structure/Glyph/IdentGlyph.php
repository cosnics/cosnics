<?php
namespace Chamilo\Libraries\Format\Structure\Glyph;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\Glyph
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class IdentGlyph extends InlineGlyph
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph::getBaseClassNames()
     */
    public function getBaseClassNames()
    {
        $baseClassNames = parent :: getBaseClassNames();

        $baseClassNames[] = 'ident';
        $baseClassNames[] = 'ident-' . $this->getType();

        return $baseClassNames;
    }
}