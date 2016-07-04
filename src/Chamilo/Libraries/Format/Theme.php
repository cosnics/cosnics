<?php
namespace Chamilo\Libraries\Format;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Theme
{
    const ICON_MINI = 16;
    const ICON_SMALL = 22;
    const ICON_MEDIUM = 32;
    const ICON_BIG = 48;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme
     */
    private static $instance;

    /**
     *
     * @var string
     */
    private $theme;

    /**
     *
     * @var string
     */
    private $namespace;

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
     * @var \Chamilo\Libraries\File\Path
     */
    private $pathUtilities;

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme
     */
    static public function getInstance()
    {
        if (is_null(static :: $instance))
        {
            $theme = \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'theme');
            $stringUtilities = StringUtilities :: getInstance();
            $classnameUtilities = ClassnameUtilities :: getInstance();
            $pathUtilities = Path :: getInstance();

            self :: $instance = new static($theme, $stringUtilities, $classnameUtilities, $pathUtilities);
        }

        return static :: $instance;
    }

    /**
     * Constructor
     *
     * @param string $theme
     * @param StringUtilities $stringUtilities
     * @param ClassnameUtilities $classnameUtilities
     * @param Path $pathUtilities
     */
    public function __construct($theme, StringUtilities $stringUtilities, ClassnameUtilities $classnameUtilities,
        Path $pathUtilities)
    {
        $this->theme = $theme;
        $this->stringUtilities = $stringUtilities;
        $this->classnameUtilities = $classnameUtilities;
        $this->pathUtilities = $pathUtilities;
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

    /**
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     *
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
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
    public function setStringUtilities($stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
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
     * @return \Chamilo\Libraries\File\Path
     */
    public function getPathUtilities()
    {
        return $this->pathUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     */
    public function setPathUtilities($pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @param boolean $includeTheme If True path will contain the selected theme as well, e.g. .../Chamilo/Configuration/Resources/Css/Aqua/.
     *                              Else, selected theme will be ignored, e.g. .../Chamilo/Configuration/Resources/Css/
     * @return string
     */
    public function getCssPath($namespace = null, $web = true, $includeTheme = true)
    {
        $directory_separator = ($web ? '/' : DIRECTORY_SEPARATOR);

        $cssPath = $this->pathUtilities->getResourcesPath($namespace, $web) . 'Css' . $directory_separator;

        if ($includeTheme)
        {
            $cssPath .= $this->getTheme() . $directory_separator;
        }

        return $cssPath;
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getStylesheetPath($namespace = null, $web = true)
    {
        return $this->getCssPath($namespace, $web) . 'Stylesheet.css';
    }

    /**
     * Backwards compatible legacy method to get the main stylesheet path
     *
     * @param boolean $web
     * @return string
     * @deprecated Use getStylesheetPath now in combination with a valid namespace
     */
    public function getCommonStylesheetPath($web = true)
    {
        return $this->getStylesheetPath('Chamilo\Configuration', $web);
    }

    /**
     *
     * @param string $context
     * @param boolean $web
     * @return string
     */
    public function getImagesPath($context = null, $web = true)
    {
        $directory_separator = ($web ? '/' : DIRECTORY_SEPARATOR);

        if (! $context)
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $calledClass = $backtrace[1]['class'];
            $context = $this->getClassnameUtilities()->getNamespaceFromClassname($calledClass);
        }

        return $this->pathUtilities->getResourcesPath($context, $web) . 'Images' . $directory_separator .
             $this->getTheme() . $directory_separator;
    }

    /**
     *
     * @param string $type
     * @param integer $size
     * @param boolean $web
     * @return string
     */
    public function getFileExtension($type, $size = self :: ICON_MINI, $web = true)
    {
        $directory_separator = ($web ? '/' : DIRECTORY_SEPARATOR);
        return $this->pathUtilities->getResourcesPath('Chamilo\Configuration', $web) . 'File' . $directory_separator .
             'Extension' . $directory_separator . $type . $directory_separator . $size . '.png';
    }

    /**
     *
     * @return string[]
     */
    public function getAvailableThemes()
    {
        $options = array();

        $path = $this->getCssPath('Chamilo\Configuration', false, false);
        $directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);

        foreach ($directories as $index => & $directory)
        {
            if (substr($directory, 0, 1) != '.')
            {
                $options[$directory] = (string) $this->stringUtilities->createString($directory)->upperCamelize();
            }
        }

        return $options;
    }

    /**
     *
     * @param string $image
     * @param string $extension
     * @param string $label
     * @param string $href
     * @param int $display
     * @param boolean $confirmation
     * @param string $context
     */
    public function getImage($image, $extension = 'png', $label = null, $href = null,
        $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false, $context = null)
    {
        if (! $context)
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $calledClass = $backtrace[1]['class'];
            $context = $this->classnameUtilities->getNamespaceFromClassname($calledClass);
        }

        $icon = new ToolbarItem(
            $label,
            $this->getImagePath($context, $image, $extension),
            $href,
            $display,
            $confirmation);
        return $icon->as_html();
    }

    /**
     *
     * @param string $image
     * @param string $extension
     * @param string $label
     * @param string $href
     * @param int $display
     * @param boolean $confirmation
     */
    public function getCommonImage($image, $extension = 'png', $label = null, $href = null,
        $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        return $this->getImage($image, $extension, $label, $href, $display, $confirmation, 'Chamilo\Configuration');
    }

    /**
     *
     * @param string $context
     * @param string $image
     * @param string $extension
     * @param boolean $web
     * @return string
     */
    public function getImagePath($context, $image, $extension = 'png', $web = true)
    {
        return $this->getImagesPath($context, $web) . $image . '.' . $extension;
    }

    public function getCommonImagePath($image, $extension = 'png', $web = true)
    {
        return $this->getImagePath('Chamilo\Configuration', $image, $extension, $web);
    }
}
