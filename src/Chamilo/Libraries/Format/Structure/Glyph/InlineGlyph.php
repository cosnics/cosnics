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
     * @var string[]
     */
    private array $extraClasses;

    private ?string $title;

    private string $type;

    public function __construct(string $type, array $extraClasses = [], ?string $title = null)
    {
        $this->type = $type;
        $this->extraClasses = $extraClasses;
        $this->title = $title;
    }

    public function render(): string
    {
        $title = $this->getTitle() ? ' title="' . htmlentities($this->getTitle()) . '"' : '';

        return '<span class="' . $this->getClassNamesString() . '"' . $title . '></span>';
    }

    /**
     * @return string[]
     */
    public function getBaseClassNames(): array
    {
        $baseClassNames[] = 'inline-glyph';

        return $baseClassNames;
    }

    /**
     * @return string[]
     */
    public function getClassNames(): array
    {
        $classes = $this->getBaseClassNames();

        foreach ($this->getExtraClasses() as $extraClass)
        {
            $classes[] = $extraClass;
        }

        return $classes;
    }

    /**
     * @return string
     */
    public function getClassNamesString(): string
    {
        return implode(' ', $this->getClassNames());
    }

    /**
     * @return string[]
     */
    public function getExtraClasses(): array
    {
        return $this->extraClasses;
    }

    /**
     *
     * @param string[]
     */
    public function setExtraClasses(array $extraClasses)
    {
        $this->extraClasses = $extraClasses;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }
}