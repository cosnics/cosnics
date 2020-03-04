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
    /**
     * @param string $namespace
     * @param boolean $isAligned
     * @param boolean $isNew
     * @param boolean $isDisabled
     * @param string[] $extraClasses
     * @param string $title
     * @param string $style
     */
    public function __construct(
        $namespace, $isAligned = false, $isNew = false, $isDisabled = false, $extraClasses = array(), $title = null,
        $style = 'fas-ci'
    )
    {
        parent::__construct(md5($namespace), $isAligned, $isNew, $isDisabled, $extraClasses, $title, $style);
    }
}