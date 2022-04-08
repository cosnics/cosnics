<?php
namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This class is an extension of twig to support resource management
 *
 * @package Chamilo\Libraries\Format\Twig\Extension
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceManagementExtension extends AbstractExtension
{
    const DEFAULT_CONTEXT = 'Chamilo\Libraries';

    /**
     * @var \Chamilo\Libraries\Format\Utilities\ResourceManager
     */
    protected $resourceManager;

    /**
     * Helper variable for the getUniquePath function
     *
     * @var string[]
     */
    protected $usedPaths;

    /**
     * ResourceManagementExtension constructor.
     *
     * @param \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
     */
    public function __construct(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }

    /**
     * @param $resourceWebPath
     *
     * @return string
     */
    protected function addModificationTimeToResourcePath($resourceWebPath)
    {
        $pathUtil = Path::getInstance();
        $webPath = $pathUtil->getBasePath(true);
        $basePath = $pathUtil->getBasePath() . '../web/';

        $systemPath = str_replace($webPath, $basePath, $resourceWebPath);
        $modificationTime = filemtime($systemPath);

        return $resourceWebPath . '?' . $modificationTime;
    }

    /**
     * Returns a css link as HTML code for a given css file and a context
     *
     * @param string $css
     * @param string $context
     *
     * @return string
     */
    public function getCss($css, $context)
    {
        $cssPath = $this->getCssPath($css, $context);

        return $this->getCssHtml($cssPath);
    }

    /**
     * Helper function to generate HTML code for css
     *
     * @param string $cssPath
     *
     * @return string
     */
    protected function getCssHtml($cssPath)
    {
        if (!$this->isUniquePath($cssPath))
        {
            return '';
        }

        $this->resourceManager->addPathToLoadedResources($cssPath);
        $cssPath = $this->addModificationTimeToResourcePath($cssPath);

        return '<link href="' . $cssPath . '" type="text/css" rel="stylesheet" />';
    }

    /**
     * Returns the path to a css in a given context, depending on the selected theme from the user
     *
     * @param string $css
     * @param string $context
     *
     * @return string
     */
    public function getCssPath($css, $context)
    {
        $context = $context ?: self::DEFAULT_CONTEXT;

        return $this->getThemePathBuilder()->getCssPath($context) . $css;
    }

    /**
     *
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('getImagePath', array($this, 'getImagePath')),
            new TwigFunction('getImage', array($this, 'getImage'), array('is_safe' => array('html'))),
            new TwigFunction('getCssPath', array($this, 'getCssPath')),
            new TwigFunction('getCss', array($this, 'getCss'), array('is_safe' => array('html'))),
            new TwigFunction('getVendorCss', array($this, 'getVendorCss'), array('is_safe' => array('html'))),
            new TwigFunction('getJavascriptPath', array($this, 'getJavascriptPath')),
            new TwigFunction('getJavascript', array($this, 'getJavascript'), array('is_safe' => array('html'))),
            new TwigFunction('getVendorJavascript', array($this, 'getVendorJavascript'),
                array('is_safe' => array('html'))
            ),
            new TwigFunction('isUniquePath', array($this, 'isUniquePath'))
        );
    }

    /**
     * Returns an image as HTML code for a given image in a given context with a given extension.
     * Optionally adding
     * the possibility to add a label, a class and a style
     *
     * @param string $image
     * @param string $context
     * @param string $extension
     * @param string $label
     * @param string $class
     * @param string $style
     *
     * @return string
     */
    public function getImage($image, $context, $extension = 'png', $label = null, $class = null, $style = null)
    {
        $imagePath = $this->getImagePath($image, $context, $extension);

        $html = [];

        $html[] = '<img border="0" src="' . $imagePath . '"';

        if (!empty($label))
        {
            $html[] = 'alt="' . $label . '" title="' . htmlentities($label) . '"';
        }

        if (!empty($class))
        {
            $html[] = 'class="' . $class . '"';
        }

        if (!empty($style))
        {
            $html[] = 'style="' . $style . '"';
        }

        $html[] = '/>';

        return implode(' ', $html);
    }

    /**
     * Returns the path to an image in a given context, depending on the selected theme from the user
     *
     * @param string $image
     * @param string $context
     * @param string $extension
     *
     * @return string
     */
    public function getImagePath($image, $context, $extension = 'png')
    {
        $context = $context ?: self::DEFAULT_CONTEXT;

        return $this->getThemePathBuilder()->getImagePath($context, $image, $extension);
    }

    /**
     * Returns a javascript link as HTML code for a given javascript file in a given context
     *
     * @param string $javascript
     * @param string $context
     *
     * @return string
     */
    public function getJavascript($javascript, $context)
    {
        $javascriptPath = $this->getJavascriptPath($javascript, $context);

        return $this->getJavascriptHtml($javascriptPath);
    }

    /**
     * Returns a javascript link as HTML code for a given javascript file in a given context
     *
     * @param string $javascriptPath
     *
     * @return string
     */
    protected function getJavascriptHtml($javascriptPath)
    {
        if (!$this->isUniquePath($javascriptPath))
        {
            return '';
        }

        $this->resourceManager->addPathToLoadedResources($javascriptPath);
        $javascriptPath = $this->addModificationTimeToResourcePath($javascriptPath);

        return '<script src="' . $javascriptPath . '" type="text/javascript" ></script>';
    }

    /**
     * Returns the path to a javascript file in a given context
     *
     * @param string $javascript
     * @param string $context
     *
     * @return string
     */
    public function getJavascriptPath($javascript, $context)
    {
        $context = $context ?: self::DEFAULT_CONTEXT;

        return Path::getInstance()->getJavascriptPath($context, true) . $javascript;
    }

    /**
     *
     * @see Twig_Extension::getName()
     */
    public function getName()
    {
        return 'resource_management';
    }

    /**
     * @param string $javascript
     * @param string $context
     *
     * @return string
     */
    public function getPluginJavascript($javascript, $context)
    {
        $javascriptPath = $this->getPluginPath($javascript, $context);

        return $this->getJavascriptHtml($javascriptPath);
    }

    /**
     * Returns the path to a plugin file in a given context
     *
     * @param string $javascript
     * @param string $context
     *
     * @return string
     */
    public function getPluginPath($javascript, $context)
    {
        $context = $context ?: self::DEFAULT_CONTEXT;

        return Path::getInstance()->getPluginPath($context, true) . $javascript;
    }

    /**
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemePathBuilder()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ThemePathBuilder::class);
    }

    /**
     * Returns a CSS link as HTML code for a given vendor css file
     *
     * @param string $cssPath
     *
     * @return string
     */
    public function getVendorCss($cssPath)
    {
        $vendorCssPath = Path::getInstance()->getBasePath(true) . 'vendor/' . $cssPath;

        return $this->getCssHtml($vendorCssPath);
    }

    /**
     * Returns a javascript link as HTML code for a given vendor javascript file
     *
     * @param string $javascriptPath
     *
     * @return string
     */
    public function getVendorJavascript($javascriptPath)
    {
        $javascriptPath = Path::getInstance()->getBasePath(true) . 'vendor/' . $javascriptPath;

        return $this->getJavascriptHtml($javascriptPath);
    }

    /**
     * Checks if a given path is unique and stores it in the usedPaths variable if it is
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isUniquePath($path)
    {
        if (in_array($path, $this->usedPaths))
        {
            return false;
        }

        $this->usedPaths[] = $path;

        if ($this->resourceManager->resource_loaded($path))
        {
            return false;
        }

        return true;
    }
}