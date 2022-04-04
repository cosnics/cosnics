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
    const ICON_BIG = 48;
    const ICON_MEDIUM = 32;
    const ICON_MINI = 16;
    const ICON_SMALL = 22;

    /**
     *
     * @var string
     */
    private $theme;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     * Constructor
     *
     * @param string $theme
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
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
     *
     * @return string[]
     */
    public function getAvailableThemes()
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

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities()
    {
        return $this->classnameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities($classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     *
     * @param string $image
     * @param string $extension
     * @param string $label
     * @param string $href
     * @param integer $display
     * @param boolean $confirmation
     *
     * @return string
     */
    public function getCommonImage(
        $image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem::DISPLAY_ICON_AND_LABEL,
        $confirmation = false
    )
    {
        return $this->getImage($image, $extension, $label, $href, $display, $confirmation, 'Chamilo\Configuration');
    }

    /**
     *
     * @param string $image
     * @param string $extension
     * @param boolean $web
     *
     * @return string
     */
    public function getCommonImagePath($image, $extension = 'png', $web = true)
    {
        return $this->getImagePath('Chamilo\Configuration', $image, $extension, $web);
    }

    /**
     * Backwards compatible legacy method to get the main stylesheet path
     *
     * @param boolean $web
     *
     * @return string
     * @deprecated Use getStylesheetPath now in combination with a valid namespace
     */
    public function getCommonStylesheetPath($web = true)
    {
        return $this->getStylesheetPath('Chamilo\Configuration', $web);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @param boolean $includeTheme If True path will contain the selected theme as well, e.g.
     *        .../Chamilo/Configuration/Resources/Css/Aqua/.
     *        Else, selected theme will be ignored, e.g. .../Chamilo/Configuration/Resources/Css/
     *
     * @return string
     */
    public function getCssPath($namespace = null, $web = true, $includeTheme = true)
    {
        $cssPath = $this->getPathBuilder()->getCssPath($namespace, $web);

        if ($includeTheme)
        {
            $cssPath .= $this->getTheme() . $this->getDirectorySeparator($web);
        }

        return $cssPath;
    }

    /**
     * @param boolean $web
     *
     * @return string
     */
    public function getDirectorySeparator(bool $web = true)
    {
        return ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    public function getFavouriteIcon()
    {
        return $this->getImagePath('Chamilo\Libraries', 'Favicon', 'ico');
    }

    /**
     *
     * @param string $image
     * @param string $extension
     * @param string $label
     * @param string $href
     * @param integer $display
     * @param boolean $confirmation
     * @param string $context
     *
     * @return string
     */
    public function getImage(
        $image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem::DISPLAY_ICON_AND_LABEL,
        $confirmation = false, $context = null
    )
    {
        if (!$context)
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $calledClass = $backtrace[1]['class'];
            $context = $this->getClassnameUtilities()->getNamespaceFromClassname($calledClass);
        }

        $icon = new ToolbarItem(
            $label, $this->getImagePath($context, $image, $extension), $href, $display, $confirmation
        );

        return $icon->render();
    }

    /**
     *
     * @param string $context
     * @param string $image
     * @param string $extension
     * @param boolean $web
     *
     * @return string
     */
    public function getImagePath($context, $image, $extension = 'png', $web = true)
    {
        return $this->getImagesPath($context, $web) . $image . '.' . $extension;
    }

    /**
     *
     * @param string $context
     * @param boolean $web
     *
     * @return string
     */
    public function getImagesPath($context = null, $web = true)
    {
        if (!$context)
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $calledClass = $backtrace[1]['class'];
            $context = $this->getClassnameUtilities()->getNamespaceFromClassname($calledClass);
        }

        return $this->getPathBuilder()->getImagesPath($context, $web) . $this->getTheme() .
            $this->getDirectorySeparator($web);
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder()
    {
        return $this->pathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function setPathBuilder($pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @param boolean $minified
     *
     * @return string
     */
    public function getStylesheetPath($namespace = null, $web = true, $minified = false)
    {
        return $this->getCssPath($namespace, $web) . 'Stylesheet' . ($minified ? '.min' : '') . '.css';
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @param boolean $includeTheme
     *
     * @return string
     */
    public function getTemplatePath($namespace = null, $web = true, $includeTheme = true)
    {
        $cssPath = $this->getPathBuilder()->getTemplatesPath($namespace, $web);

        if ($includeTheme)
        {
            $cssPath .= $this->getTheme() . $this->getDirectorySeparator($web);
        }

        return $cssPath;
    }

    /**
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     *
     * @param string $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }
}