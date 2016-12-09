<?php
namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;

/**
 * This class is an extension of twig to support resource management
 * 
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceManagementExtension extends \Twig_Extension
{
    const DEFAULT_CONTEXT = 'Chamilo\Libraries';

    /**
     * Helper variable for the getUniquePath function
     * 
     * @var string[]
     */
    private $usedPaths;

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getImagePath', array($this, 'getImagePath')), 
            new \Twig_SimpleFunction('getImage', array($this, 'getImage'), array('is_safe' => array('html'))), 
            new \Twig_SimpleFunction('getCssPath', array($this, 'getCssPath')), 
            new \Twig_SimpleFunction('getCss', array($this, 'getCss'), array('is_safe' => array('html'))), 
            new \Twig_SimpleFunction('getVendorCss', array($this, 'getVendorCss'), array('is_safe' => array('html'))), 
            new \Twig_SimpleFunction('getJavascriptPath', array($this, 'getJavascriptPath')), 
            new \Twig_SimpleFunction('getJavascript', array($this, 'getJavascript'), array('is_safe' => array('html'))), 
            new \Twig_SimpleFunction(
                'getVendorJavascript', 
                array($this, 'getVendorJavascript'), 
                array('is_safe' => array('html'))), 
            new \Twig_SimpleFunction('isUniquePath', array($this, 'isUniquePath')));
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
        $context = $context ?: self::HOGENT_LIBRARIES_CONTEXT;
        
        return Theme::getInstance()->getImagePath($context, $image, $extension);
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
        
        $html = array();
        
        $html[] = '<img border="0" src="' . $imagePath . '"';
        
        if (! empty($label))
        {
            $html[] = 'alt="' . $label . '" title="' . htmlentities($label) . '"';
        }
        
        if (! empty($class))
        {
            $html[] = 'class="' . $class . '"';
        }
        
        if (! empty($style))
        {
            $html[] = 'style="' . $style . '"';
        }
        
        $html[] = '/>';
        
        return implode(' ', $html);
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
        
        return Theme::getInstance()->getCssPath($context) . $css;
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
     * Helper function to generate HTML code for css
     * 
     * @param string $cssPath
     *
     * @return string
     */
    protected function getCssHtml($cssPath)
    {
        if (! $this->isUniquePath($cssPath))
        {
            return '';
        }
        
        return '<link href="' . $cssPath . '" type="text/css" rel="stylesheet" />';
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
        
        return Path::getInstance()->namespaceToFullPath($context, true) . 'Resources/Javascript/' . $javascript;
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
     * Returns a javascript link as HTML code for a given javascript file in a given context
     * 
     * @param string $javascriptPath
     *
     * @return string
     */
    protected function getJavascriptHtml($javascriptPath)
    {
        return '<script src="' . $javascriptPath . '" type="text/javascript" ></script>';
    }

    /**
     * Checks if a given path is unique and stores it in the usedPaths variable if it is
     * 
     * @param string $path
     *
     * @return bool
     */
    public function isUniquePath($path)
    {
        if (! in_array($path, $this->usedPaths))
        {
            $this->usedPaths[] = $path;
            
            return true;
        }
        
        return false;
    }

    /**
     * Returns the name of the extension.
     * 
     * @return string The extension name
     */
    public function getName()
    {
        return 'resource_management';
    }
}