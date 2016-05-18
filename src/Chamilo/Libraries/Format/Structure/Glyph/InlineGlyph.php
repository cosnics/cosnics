<?php
namespace Chamilo\Libraries\Format\Structure\Glyph;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\Glyph
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class InlineGlyph
{

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var string[]
     */
    private $extraClasses;

    /**
     *
     * @param string $type
     * @param string[] $extraClasses
     */
    public function __construct($type, $extraClasses = array())
    {
        $this->type = $type;
        $this->extraClasses = $extraClasses;
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
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return string[]
     */
    public function getExtraClasses()
    {
        return $this->extraClasses;
    }

    /**
     *
     * @param string[]
     */
    public function setExtraClasses($extraClasses)
    {
        $this->extraClasses = $extraClasses;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        return '<span class="' . implode(' ', $this->getClassNames()) . '"></span>';
    }

    /**
     *
     * @return string[]
     */
    public function getBaseClassNames()
    {
        $baseClassNames[] = 'inline-glyph';

        return $baseClassNames;
    }

    /**
     *
     * @return string[]
     */
    public function getClassNames()
    {
        return array_merge($this->getBaseClassNames(), $this->getExtraClasses());
    }
}