<?php
namespace Chamilo\Libraries\UserInterface\Theme\Service;

use Chamilo\Core\Admin\Manager;
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
    public function __construct(
        protected StringUtilities $stringUtilities, protected AbstractPathBuilder $pathBuilder,
        protected FilesystemTools $filesystemTools, protected string $theme
    )
    {
    }

    /**
     * @return string[]
     */
    public function getAvailableThemes(): array
    {
        $availableThemes = [];

        $path = $this->getCssPath(Manager::CONTEXT, false);
        $directories = $this->filesystemTools->getDirectoryContent($path, FileTypeFilterIterator::ONLY_FILES, false);

        foreach ($directories as $directory) {
            if (!str_starts_with($directory, '.')) {
                $availableThemes[$directory] =
                    (string) $this->stringUtilities->createString($directory)->upperCamelize();
            }
        }

        return $availableThemes;
    }

    public function getCssPath(string $namespace, bool $includeTheme = true): string
    {
        $cssPath = $this->pathBuilder->getCssPath($namespace);

        if ($includeTheme) {
            $cssPath .= $this->theme . $this->getDirectorySeparator();
        }

        return $cssPath;
    }

    public function getDirectorySeparator(): string
    {
        return $this->pathBuilder->getDirectorySeparator();
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
        return $this->pathBuilder->getImagesPath($context) . $this->theme . $this->getDirectorySeparator();
    }

    public function getTemplatePath(string $namespace, bool $includeTheme = true): string
    {
        $cssPath = $this->pathBuilder->getTemplatesPath($namespace);

        if ($includeTheme) {
            $cssPath .= $this->theme . $this->getDirectorySeparator();
        }

        return $cssPath;
    }
}
