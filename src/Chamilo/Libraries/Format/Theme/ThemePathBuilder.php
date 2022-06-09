<?php
namespace Chamilo\Libraries\Format\Theme;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Theme
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ThemePathBuilder
{
    public const ICON_BIG = 48;
    public const ICON_MEDIUM = 32;
    public const ICON_MINI = 16;
    public const ICON_SMALL = 22;

    private ClassnameUtilities $classnameUtilities;

    private PathBuilder $pathBuilder;

    private StringUtilities $stringUtilities;

    private string $theme;

    public function __construct(
        StringUtilities $stringUtilities, ClassnameUtilities $classnameUtilities, PathBuilder $pathBuilder,
        string $theme
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->classnameUtilities = $classnameUtilities;
        $this->pathBuilder = $pathBuilder;
        $this->theme = $theme;
    }

    /**
     * @return string[]
     */
    public function getAvailableThemes(): array
    {
        $availableThemes = [];

        $path = $this->getCssPath('Chamilo\Configuration', false, false);
        $directories = Filesystem::get_directory_content($path, Filesystem::LIST_DIRECTORIES, false);

        foreach ($directories as $index => $directory)
        {
            if (substr($directory, 0, 1) != '.')
            {
                $availableThemes[$directory] =
                    (string) $this->getStringUtilities()->createString($directory)->upperCamelize();
            }
        }

        return $availableThemes;
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    public function getCommonImage(
        string $image, string $extension = 'png', ?string $label = null, ?string $href = null,
        int $display = ToolbarItem::DISPLAY_ICON_AND_LABEL, bool $confirmation = false
    ): string
    {
        return $this->getImage(StringUtilities::LIBRARIES, $image, $extension, $label, $href, $display, $confirmation);
    }

    /**
     * Backwards compatible legacy method to get the a common image
     *
     * @deprecated Use ThemePathBuilder::getImagePath() now in combination with a valid namespace
     */
    public function getCommonImagePath(string $image, string $extension = 'png', bool $web = true): string
    {
        return $this->getImagePath('Chamilo\Configuration', $image, $extension, $web);
    }

    /**
     * Backwards compatible legacy method to get the main stylesheet path
     *
     * @deprecated Use ThemePathBuilder::getStylesheetPath() now in combination with a valid namespace
     */
    public function getCommonStylesheetPath(bool $web = true): string
    {
        return $this->getStylesheetPath('Chamilo\Configuration', $web);
    }

    public function getCssPath(string $namespace, bool $web = true, bool $includeTheme = true): string
    {
        $cssPath = $this->getPathBuilder()->getCssPath($namespace, $web);

        if ($includeTheme)
        {
            $cssPath .= $this->getTheme() . $this->getDirectorySeparator($web);
        }

        return $cssPath;
    }

    public function getDirectorySeparator(bool $web = true): string
    {
        return ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    public function getFavouriteIcon(): string
    {
        return $this->getImagePath('Chamilo\Libraries', 'Favicon', 'ico');
    }

    public function getImage(
        string $context, string $image, string $extension = 'png', ?string $label = null, ?string $href = null,
        int $display = ToolbarItem::DISPLAY_ICON_AND_LABEL, bool $confirmation = false
    ): string
    {
        $icon = new ToolbarItem(
            $label, $this->getImagePath($context, $image, $extension), $href, $display, $confirmation
        );

        return $icon->render();
    }

    public function getImagePath(string $context, string $image, string $extension = 'png', bool $web = true): string
    {
        return $this->getImagesPath($context, $web) . $image . '.' . $extension;
    }

    public function getImagesPath(string $context, bool $web = true): string
    {
        return $this->getPathBuilder()->getImagesPath($context, $web) . $this->getTheme() .
            $this->getDirectorySeparator($web);
    }

    public function getPathBuilder(): PathBuilder
    {
        return $this->pathBuilder;
    }

    public function setPathBuilder(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    public function getStylesheetPath(?string $namespace = null, bool $web = true, bool $minified = false): string
    {
        return $this->getCssPath($namespace, $web) . 'Stylesheet' . ($minified ? '.min' : '') . '.css';
    }

    public function getTemplatePath(string $namespace, bool $web = true, bool $includeTheme = true): string
    {
        $cssPath = $this->getPathBuilder()->getTemplatesPath($namespace, $web);

        if ($includeTheme)
        {
            $cssPath .= $this->getTheme() . $this->getDirectorySeparator($web);
        }

        return $cssPath;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme)
    {
        $this->theme = $theme;
    }
}
