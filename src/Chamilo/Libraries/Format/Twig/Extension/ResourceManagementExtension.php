<?php
namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\StringUtilities;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This class is an extension of twig to support resource management
 *
 * @package Chamilo\Libraries\Format\Twig\Extension
 * @author  Sven Vanpoucke - Hogeschool Gent
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
        $webPath = $this->getWebPathBuilder()->getBasePath();
        $basePath = $this->getSystemPathBuilder()->getBasePath() . '../web/';

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
        return $this->getThemeWebPathBuilder()->getCssPath($context) . $css;
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
     * Returns the path to an image in a given context, depending on the selected theme from the user
     */
    public function getImagePath(string $image, string $context = StringUtilities::LIBRARIES, string $extension = 'png'
    ): string
    {
        return $this->getThemeWebPathBuilder()->getImagePath($context, $image, $extension);
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
        return $this->getWebPathBuilder()->getJavascriptPath($context) . $javascript;
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
        return $this->getWebPathBuilder()->getPluginPath($context) . $javascript;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            WebPathBuilder::class
        );
    }

    public function getThemeWebPathBuilder(): ThemePathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Libraries\Format\Theme\ThemeWebPathBuilder'
        );
    }

    public function getVendorCss(string $cssPath): string
    {
        $webPathBuilder = $this->getWebPathBuilder();
        $vendorCssPath =
            $webPathBuilder->getBasePath() . 'vendor' . $webPathBuilder->getDirectorySeparator() . $cssPath;

        return $this->getCssHtml($vendorCssPath);
    }

    public function getVendorJavascript(string $javascriptPath): string
    {
        $webPathBuilder = $this->getWebPathBuilder();
        $javascriptPath =
            $webPathBuilder->getBasePath() . 'vendor' . $webPathBuilder->getDirectorySeparator() . $javascriptPath;

        return $this->getJavascriptHtml($javascriptPath);
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            WebPathBuilder::class
        );
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