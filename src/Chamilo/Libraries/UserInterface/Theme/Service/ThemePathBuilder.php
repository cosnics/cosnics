<?php
namespace Chamilo\Libraries\UserInterface\Theme\Service;

use Chamilo\Libraries\Filesystem\Service\AbstractPathBuilder;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

/**
 * @package Chamilo\Libraries\UserInterface\Theme\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ThemePathBuilder
{
    protected FilesystemTools $filesystemTools;

    private AbstractPathBuilder $pathBuilder;

    private StringUtilities $stringUtilities;

    private string $theme;

    public function __construct(
        StringUtilities $stringUtilities, AbstractPathBuilder $pathBuilder, FilesystemTools $filesystemTools,
        string $theme
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->pathBuilder = $pathBuilder;
        $this->filesystemTools = $filesystemTools;
        $this->theme = $theme;
    }

    /**
     * @return string[]
     */
    public function getAvailableThemes(): array
    {
        $availableThemes = [];

        $path = $this->getCssPath('Chamilo\Core\Admin', false);
        $directories =
            $this->getFilesystemTools()->getDirectoryContent($path, FileTypeFilterIterator::ONLY_FILES, false);

        foreach ($directories as $directory) {
            if (!str_starts_with($directory, '.')) {
                $availableThemes[$directory] =
                    (string) $this->getStringUtilities()->createString($directory)->upperCamelize();
            }
        }

        return $availableThemes;
    }

    public function getCssPath(string $namespace, bool $includeTheme = true): string
    {
        $cssPath = $this->getPathBuilder()->getCssPath($namespace);

        if ($includeTheme) {
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

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
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

    public function getTemplatePath(string $namespace, bool $includeTheme = true): string
    {
        $cssPath = $this->getPathBuilder()->getTemplatesPath($namespace);

        if ($includeTheme) {
            $cssPath .= $this->getTheme() . $this->getDirectorySeparator();
        }

        return $cssPath;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }
}
