<?php
namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\StringUtilities;
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
    protected ResourceManager $resourceManager;

    /**
     * @var string[]
     */
    protected array $usedPaths;

    public function __construct(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }

    protected function addModificationTimeToResourcePath(string $resourceWebPath): string
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
     */
    public function getCss(string $css, string $context): string
    {
        $cssPath = $this->getCssPath($css, $context);

        return $this->getCssHtml($cssPath);
    }

    protected function getCssHtml(string $cssPath): string
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
     */
    public function getCssPath(string $css, string $context = StringUtilities::LIBRARIES): string
    {
        return $this->getThemePathBuilder()->getCssPath($context) . $css;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getImagePath', [$this, 'getImagePath']),
            new TwigFunction('getImage', [$this, 'getImage'], ['is_safe' => ['html']]),
            new TwigFunction('getCssPath', [$this, 'getCssPath']),
            new TwigFunction('getCss', [$this, 'getCss'], ['is_safe' => ['html']]),
            new TwigFunction('getVendorCss', [$this, 'getVendorCss'], ['is_safe' => ['html']]),
            new TwigFunction('getJavascriptPath', [$this, 'getJavascriptPath']),
            new TwigFunction('getJavascript', [$this, 'getJavascript'], ['is_safe' => ['html']]),
            new TwigFunction('getVendorJavascript', [$this, 'getVendorJavascript'], ['is_safe' => ['html']]),
            new TwigFunction('isUniquePath', [$this, 'isUniquePath'])
        ];
    }

    /**
     * Returns an image as HTML code for a given image in a given context with a given extension. Optionally adding the
     * possibility to add a label, a class and a style
     */
    public function getImage(
        string $image, string $context, string $extension = 'png', ?string $label = null, ?string $class = null,
        ?string $style = null
    ): string
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
     */
    public function getImagePath(string $image, string $context = StringUtilities::LIBRARIES, string $extension = 'png'): string
    {
        return $this->getThemePathBuilder()->getImagePath($context, $image, $extension);
    }

    /**
     * Returns a javascript link as HTML code for a given javascript file in a given context
     */
    public function getJavascript(string $javascript, string $context): string
    {
        $javascriptPath = $this->getJavascriptPath($javascript, $context);

        return $this->getJavascriptHtml($javascriptPath);
    }

    /**
     * Returns a javascript link as HTML code for a given javascript file in a given context
     */
    protected function getJavascriptHtml(string $javascriptPath): string
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
     */
    public function getJavascriptPath(string $javascript, string $context = StringUtilities::LIBRARIES): string
    {
        return Path::getInstance()->getJavascriptPath($context, true) . $javascript;
    }

    public function getName(): string
    {
        return 'resource_management';
    }

    public function getPluginJavascript(string $javascript, string $context): string
    {
        $javascriptPath = $this->getPluginPath($javascript, $context);

        return $this->getJavascriptHtml($javascriptPath);
    }

    public function getPluginPath(string $javascript, string $context = StringUtilities::LIBRARIES): string
    {
        return Path::getInstance()->getPluginPath($context, true) . $javascript;
    }

    public function getThemePathBuilder(): ThemePathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ThemePathBuilder::class);
    }

    public function getVendorCss(string $cssPath): string
    {
        $vendorCssPath = Path::getInstance()->getBasePath(true) . 'vendor/' . $cssPath;

        return $this->getCssHtml($vendorCssPath);
    }

    public function getVendorJavascript(string $javascriptPath): string
    {
        $javascriptPath = Path::getInstance()->getBasePath(true) . 'vendor/' . $javascriptPath;

        return $this->getJavascriptHtml($javascriptPath);
    }

    public function isUniquePath(string $path): bool
    {
        if (in_array($path, $this->usedPaths))
        {
            return false;
        }

        $this->usedPaths[] = $path;

        if ($this->resourceManager->hasResourceAlreadyBeenLoaded($path))
        {
            return false;
        }

        return true;
    }
}