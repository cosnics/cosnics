<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Architecture\Traits\ClassContext;

/**
 * @package Chamilo\Libraries\Format\Table\Column
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class TableColumn
{
    use ClassContext;

    public const CSS_CLASSES_COLUMN_CONTENT = 'content';
    public const CSS_CLASSES_COLUMN_HEADER = 'header';

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

        if ($headerCssClasses)
        {
            $this->cssClasses[self::CSS_CLASSES_COLUMN_HEADER] = $headerCssClasses;
        }

        if ($contentCssClasses)
        {
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

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_title(): string
    {
        return $this->title;
    }

    /**
     * @throws \ReflectionException
     */
    public static function package(): string
    {
        return static::context();
    }

    /**
     * @param string[][] $cssClasses
     */
    public function setCssClasses(?array $cssClasses)
    {
        $this->cssClasses = $cssClasses;
    }

    /**
     * Sets the name of this column
     *
     * @param string $name
     */
    public function set_name(string $name)
    {
        $this->name = $name;
    }

    public function set_title(string $title)
    {
        $this->title = $title;
    }
}
