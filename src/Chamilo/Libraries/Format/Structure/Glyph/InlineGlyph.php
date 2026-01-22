<?php
namespace Chamilo\Libraries\Format\Structure\Glyph;

/**
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
     * @param string[] $extraClasses
     */
    public function setExtraClasses(array $extraClasses): static
    {
        $this->extraClasses = $extraClasses;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}