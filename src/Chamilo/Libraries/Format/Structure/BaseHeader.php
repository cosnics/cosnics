<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Ajax\Component\JavascriptComponent;
use Chamilo\Libraries\Ajax\Component\StylesheetComponent;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Cache\Assetic\JavascriptCacheService;
use Chamilo\Libraries\Cache\Assetic\StylesheetCommonCacheService;
use Chamilo\Libraries\Cache\Assetic\StylesheetVendorCacheService;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BaseHeader implements HeaderInterface
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var string
     */
    private $title;

    /**
     *
     * @var integer
     */
    private $viewMode;

    /**
     *
     * @var string
     */
    private $containerMode;

    /**
     * The html headers which will be added in the <head> tag of the html document.
     *
     * @var string[]
     */
    private $htmlHeaders;

    /**
     *
     * @var string
     */
    private $languageCode;

    /**
     *
     * @var string
     */
    private $textDirection;

    /**
     *
     * @param integer $viewMode
     * @param string $containerMode
     * @param string $languageCode
     * @param string $textDirection
     */
    public function __construct(
        $viewMode = Page::VIEW_MODE_FULL, $containerMode = 'container-fluid', $languageCode = 'en',
        $textDirection = 'ltr'
    )
    {
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
        $this->languageCode = $languageCode;
        $this->textDirection = $textDirection;

        $this->htmlHeaders = array();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $this->addDefaultHeaders();

        $html = array();

        $html[] = '<!DOCTYPE html>';
        $html[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->getLanguageCode() . '" lang="' .
            $this->getLanguageCode() . '">';
        $html[] = '<head>';

        $htmlHeaders = $this->getHtmlHeaders();
        foreach ($htmlHeaders as $index => $htmlHeader)
        {
            $html[] = $htmlHeader;
        }

        $html[] = '</head>';

        $html[] = '<body dir="' . $this->getTextDirection() . '">';

        if ($this->getViewMode() != Page::VIEW_MODE_HEADERLESS)
        {
            $html[] = $this->getBanner()->render();
        }

        $classes = $this->getContainerMode();

        if ($this->getViewMode() == Page::VIEW_MODE_HEADERLESS)
        {
            $classes .= ' container-headerless';
        }

        $html[] = '<div class="' . $classes . '">';

        return implode(PHP_EOL, $html);
    }

    protected function addCommonCssHeader(ConfigurablePathBuilder $configurablePathBuilder, PathBuilder $pathBuilder)
    {
        $stylesheetCacheService = new StylesheetCommonCacheService(
            $pathBuilder, $configurablePathBuilder, $this->getThemePathBuilder()
        );

        $cssModified = $stylesheetCacheService->getLastModificationTime();
        $cssModified = $cssModified ? $cssModified : time();

        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = 'Chamilo\Libraries\Ajax';
        $parameters[Application::PARAM_ACTION] = 'Stylesheet';
        $parameters[StylesheetComponent::PARAM_TYPE] = StylesheetComponent::TYPE_COMMON;
        $parameters[StylesheetComponent::PARAM_THEME] = $this->getThemePathBuilder()->getTheme();
        $parameters[StylesheetComponent::PARAM_MODIFIED] = $cssModified;

        $this->addCssFile($pathBuilder->getBasePath(true) . '?' . http_build_query($parameters));
    }

    /**
     * @param string $file
     * @param string $media
     */
    public function addCssFile($file, $media = 'screen')
    {
        $header = '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $file . '" />';
        $this->addHtmlHeader($header);
    }

    /**
     * Adds some default headers to the output
     */
    protected function addDefaultHeaders()
    {
        $pathBuilder = new PathBuilder(ClassnameUtilities::getInstance());

        $fileConfigurationConsulter = new ConfigurationConsulter(
            new FileConfigurationLoader(
                new FileConfigurationLocator(new PathBuilder(new ClassnameUtilities(new StringUtilities())))
            )
        );

        $configurablePathBuilder = new ConfigurablePathBuilder(
            $fileConfigurationConsulter->getSetting(array('Chamilo\Configuration', 'storage'))
        );

        $this->addHtmlHeader('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
        $this->addHtmlHeader('<meta name="viewport" content="width=device-width, initial-scale=1">');
        $this->addHtmlHeader('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');

        $this->addVendorCssHeader($configurablePathBuilder, $pathBuilder);
        $this->addCommonCssHeader($configurablePathBuilder, $pathBuilder);

        $this->addLink($pathBuilder->getBasePath(true), 'top');
        $this->addLink(
            $this->getThemePathBuilder()->getFavouriteIcon(), 'shortcut icon', null, 'image/x-icon'
        );

        $this->addExceptionLogger($fileConfigurationConsulter);

        $this->addHtmlHeader(
            '<script>var rootWebPath="' . Path::getInstance()->getBasePath(true) . '";</script>'
        );

        $javascriptCacheService = new JavascriptCacheService($pathBuilder, $configurablePathBuilder);

        $javascriptModified = $javascriptCacheService->getLastModificationTime();
        $javascriptModified = $javascriptModified ? $javascriptModified : time();

        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = 'Chamilo\Libraries\Ajax';
        $parameters[Application::PARAM_ACTION] = 'Javascript';
        $parameters[JavascriptComponent::PARAM_MODIFIED] = $javascriptModified;
        $this->addJavascriptFile($pathBuilder->getBasePath(true) . '?' . http_build_query($parameters));

        $this->addJavascriptCDNFiles();

        $this->addHtmlHeader('<title>' . $this->getTitle() . '</title>');
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function addExceptionLogger(ConfigurationConsulter $configurationConsulter)
    {
        // Disabled this due to a lot of javascript issues being logged due to browser issues
        //        $exceptionLoggerFactory = new ExceptionLoggerFactory($configurationConsulter);
        //        $exceptionLogger = $exceptionLoggerFactory->createExceptionLogger();
        //        $exceptionLogger->addJavascriptExceptionLogger($this);
    }

    /**
     * @param string $html_header
     */
    public function addHtmlHeader($html_header)
    {
        $this->htmlHeaders[] = $html_header;
    }

    /**
     * Adds javascript files from a CDN
     */
    public function addJavascriptCDNFiles()
    {
        $this->addJavascriptFile(
            'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML'
        );
    }

    /**
     * @param string $file
     */
    public function addJavascriptFile($file)
    {
        $header[] = '<script src="' . $file . '"></script>';
        $this->addHtmlHeader(implode(' ', $header));
    }

    /**
     * @param string $url
     * @param string $rel
     * @param string $title
     * @param string $type
     */
    public function addLink($url, $rel = null, $title = null, $type = null)
    {
        $type = $type ? ' type="' . $type . '"' : '';
        $title = $title ? ' title="' . htmlentities($title) . '"' : '';
        $rel = $rel ? ' rel="' . $rel . '"' : '';
        $href = ' href="' . $url . '"';
        $header = '<link' . $href . $rel . $title . $type . '/>';
        $this->addHtmlHeader($header);
    }

    protected function addVendorCssHeader(ConfigurablePathBuilder $configurablePathBuilder, PathBuilder $pathBuilder)
    {
        $stylesheetCacheService = new StylesheetVendorCacheService(
            $pathBuilder, $configurablePathBuilder
        );

        $cssModified = $stylesheetCacheService->getLastModificationTime();
        $cssModified = $cssModified ? $cssModified : time();

        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = 'Chamilo\Libraries\Ajax';
        $parameters[Application::PARAM_ACTION] = 'Stylesheet';
        $parameters[StylesheetComponent::PARAM_TYPE] = StylesheetComponent::TYPE_VENDOR;
        $parameters[StylesheetComponent::PARAM_MODIFIED] = $cssModified;

        $this->addCssFile($pathBuilder->getBasePath(true) . '?' . http_build_query($parameters));
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Banner
     */
    protected function getBanner()
    {
        return new Banner($this->getApplication(), $this->getViewMode(), $this->getContainerMode());
    }

    /**
     *
     * @return string
     */
    public function getContainerMode()
    {
        return $this->containerMode;
    }

    /**
     *
     * @param string $containerMode
     */
    public function setContainerMode($containerMode)
    {
        $this->containerMode = $containerMode;
    }

    /**
     *
     * @return string[]
     */
    public function getHtmlHeaders()
    {
        return $this->htmlHeaders;
    }

    /**
     *
     * @param string[] $htmlHeaders
     */
    public function setHtmlHeaders($htmlHeaders)
    {
        $this->htmlHeaders = $htmlHeaders;
    }

    /**
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     *
     * @param string $languageCode
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;
    }

    /**
     *
     * @return string
     */
    public function getTextDirection()
    {
        return $this->textDirection;
    }

    /**
     *
     * @param string $textDirection
     */
    public function setTextDirection($textDirection)
    {
        $this->textDirection = $textDirection;
    }

    /**
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     * @throws \Exception
     */
    public function getThemePathBuilder()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ThemePathBuilder::class);
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return integer
     */
    public function getViewMode()
    {
        return $this->viewMode;
    }

    /**
     *
     * @param integer $viewMode
     */
    public function setViewMode($viewMode)
    {
        $this->viewMode = $viewMode;
    }

    /**
     *
     * @return string
     * @throws \Exception
     * @deprecated Use render() now
     */
    public function toHtml()
    {
        return $this->render();
    }
}
