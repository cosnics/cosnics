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
     * @var string
     */
    private $title;

    /**
     *
     * @param string $type
     * @param string[] $extraClasses
     */
    public function __construct($type, $extraClasses = array(), $title = null)
    {
        $this->type = $type;
        $this->extraClasses = $extraClasses;
        $this->title = $title;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $title = $this->getTitle() ? ' title="' . htmlentities($this->getTitle()) . '"' : '';
        return '<span class="' . implode(' ', $this->getClassNames()) . '"' . $title . '></span>';
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