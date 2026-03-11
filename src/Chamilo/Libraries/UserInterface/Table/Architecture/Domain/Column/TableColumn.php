<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class TableColumn
{
    public const string CSS_CLASSES_COLUMN_CONTENT = 'content';
    public const string CSS_CLASSES_COLUMN_HEADER = 'header';

    /**
     * @var string[][]
     */
    protected array $cssClasses = [];

    private string $name;

    private string $title;

    /**
     * @param string[] $headerCssClasses
     * @param string[] $contentCssClasses
     */
    public function __construct(
        string $name, string $title, ?array $headerCssClasses = null, ?array $contentCssClasses = null
    )
    {
        $this->name = $name;
        $this->title = $title;

        if ($headerCssClasses) {
            $this->cssClasses[self::CSS_CLASSES_COLUMN_HEADER] = $headerCssClasses;
        }

        if ($contentCssClasses) {
            $this->cssClasses[self::CSS_CLASSES_COLUMN_CONTENT] = $contentCssClasses;
        }
    }

    /**
     * @return string[][]
     */
    public function getCssClasses(): array
    {
        return $this->cssClasses;
    }

    /**
     * @param string[][] $cssClasses
     */
    public function setCssClasses(?array $cssClasses): static
    {
        $this->cssClasses = $cssClasses;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
