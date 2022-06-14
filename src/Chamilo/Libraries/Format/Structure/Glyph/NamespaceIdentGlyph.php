<?php
namespace Chamilo\Libraries\Format\Structure\Glyph;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\Glyph
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NamespaceIdentGlyph extends IdentGlyph
{
    public function __construct(
        string $namespace, bool $isAligned = false, bool $isNew = false, bool $isDisabled = false,
        int $size = IdentGlyph::SIZE_SMALL, array $extraClasses = [], ?string $title = null, string $style = 'fas-ci'
    )
    {
        parent::__construct(md5($namespace), $isAligned, $isNew, $isDisabled, $size, $extraClasses, $title, $style);
    }
}