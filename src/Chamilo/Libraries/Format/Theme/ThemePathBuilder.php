<?php
namespace Chamilo\Libraries\Format\Theme;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\AbstractPathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Theme
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ThemePathBuilder
{
    public const ICON_BIG = 48;
    public const ICON_MEDIUM = 32;
    public const ICON_MINI = 16;
    public const ICON_SMALL = 22;

    private ClassnameUtilities $classnameUtilities;

    private AbstractPathBuilder $pathBuilder;

    private StringUtilities $stringUtilities;

    private string $theme;

    public function __construct(
        StringUtilities $stringUtilities, ClassnameUtilities $classnameUtilities, AbstractPathBuilder $pathBuilder,
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

        $path = $this->getCssPath('Chamilo\Configuration', false);
        $directories = Filesystem::get_directory_content($path, Filesystem::LIST_DIRECTORIES, false);

        foreach ($directories as $directory)
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

    public function getCssPath(string $namespace, bool $includeTheme = true): string
    {
        $cssPath = $this->getPathBuilder()->getCssPath($namespace);

        if ($includeTheme)
        {
            $cssPath .= $this->getTheme() . $this->getDirectorySeparator();
        }

        return $cssPath;
    }

    public function getDirectorySeparator(): string
    {
        return $this->getPathBuilder()->getDirectorySeparator();
    }

    public function getFavouriteIcon(): string
    {
        return $this->getImagePath(StringUtilities::LIBRARIES, 'Favicon', 'ico');
    }

    public function getImagePath(string $context, string $image, string $extension = 'png'): string
    {
        return $this->getImagesPath($context) . $image . '.' . $extension;
    }

    public function getImagesPath(string $context): string
    {
        return $this->getPathBuilder()->getImagesPath($context) . $this->getTheme() . $this->getDirectorySeparator();
    }

    public function getPathBuilder(): AbstractPathBuilder
    {
        return $this->pathBuilder;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getStylesheetPath(?string $namespace = null, bool $minified = false): string
    {
        return $this->getCssPath($namespace) . 'Stylesheet' . ($minified ? '.min' : '') . '.css';
    }

    public function getTemplatePath(string $namespace, bool $includeTheme = true): string
    {
        $cssPath = $this->getPathBuilder()->getTemplatesPath($namespace);

        if ($includeTheme)
        {
            $cssPath .= $this->getTheme() . $this->getDirectorySeparator();
        }

        return $cssPath;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): ThemePathBuilder
    {
        $this->theme = $theme;

        return $this;
    }
}
