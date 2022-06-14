<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BaseHeader implements HeaderInterface
{
    use DependencyInjectionContainerTrait;

    private Application $application;

    private string $containerMode;

    /**
     * The html headers which will be added in the <head> tag of the html document.
     *
     * @var string[]
     */
    private array $htmlHeaders;

    private string $languageCode;

    private string $textDirection;

    private string $title;

    private int $viewMode;

    public function __construct(
        int $viewMode = Page::VIEW_MODE_FULL, string $containerMode = 'container-fluid', string $languageCode = 'en',
        string $textDirection = 'ltr'
    )
    {
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
        $this->languageCode = $languageCode;
        $this->textDirection = $textDirection;

        $this->htmlHeaders = [];
        $this->initializeContainer();
    }

    /**
     * @throws \Exception
     */
    public function render(): string
    {
        $this->addDefaultHeaders();

        $html = [];

        $html[] = '<!DOCTYPE html>';
        $html[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->getLanguageCode() . '" lang="' .
            $this->getLanguageCode() . '">';
        $html[] = '<head>';

        $htmlHeaders = $this->getHtmlHeaders();
        
        foreach ($htmlHeaders as $htmlHeader)
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

    public function addCssFile(string $file, string $media = 'screen')
    {
        $header = '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $file . '" />';
        $this->addHtmlHeader($header);
    }

    /**
     * @throws \Exception
     */
    protected function addDefaultHeaders()
    {
        $pathBuilder = $this->getPathBuilder();
        $themePathBuilder = $this->getThemePathBuilder();

        $this->addHtmlHeader('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
        $this->addHtmlHeader('<meta name="viewport" content="width=device-width, initial-scale=1">');
        $this->addHtmlHeader('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');

        $cssPath = $pathBuilder->getCssPath('Chamilo/Libraries', true);
        $javascriptPath = $pathBuilder->getJavascriptPath('Chamilo/Libraries', true);

        $this->addCssFile($cssPath . 'cosnics.vendor.bootstrap.min.css');
        $this->addCssFile($cssPath . 'cosnics.vendor.jquery.min.css');
        $this->addCssFile($cssPath . 'cosnics.vendor.min.css');
        $this->addCssFile($cssPath . 'cosnics.common.' . $themePathBuilder->getTheme() . '.min.css');

        $this->addLink($pathBuilder->getBasePath(true), 'top');
        $this->addLink($themePathBuilder->getFavouriteIcon(), 'shortcut icon', null, 'image/x-icon');

        $this->addExceptionLogger();

        $this->addHtmlHeader(
            '<script>var rootWebPath="' . Path::getInstance()->getBasePath(true) . '";</script>'
        );

        $this->addJavascriptFile($javascriptPath . 'cosnics.vendor.jquery.min.js');
        $this->addJavascriptFile($javascriptPath . 'cosnics.vendor.bootstrap.min.js');
        $this->addJavascriptFile($javascriptPath . 'cosnics.vendor.angular.min.js');
        $this->addJavascriptFile($javascriptPath . 'cosnics.vendor.min.js');
        $this->addJavascriptFile($javascriptPath . 'cosnics.common.min.js');

        $this->addJavascriptCDNFiles();

        $this->addHtmlHeader('<title>' . $this->getTitle() . '</title>');
    }

    public function addExceptionLogger()
    {
        // Disabled this due to a lot of javascript issues being logged due to browser issues
        //        $exceptionLoggerFactory = $this->getService(ExceptionLoggerFactory::class);
        //        $exceptionLogger = $exceptionLoggerFactory->createExceptionLogger();
        //        $exceptionLogger->addJavascriptExceptionLogger($this);
    }

    public function addHtmlHeader(string $htmlHeader)
    {
        $this->htmlHeaders[] = $htmlHeader;
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

    public function addJavascriptFile(string $file)
    {
        $header[] = '<script src="' . $file . '"></script>';
        $this->addHtmlHeader(implode(' ', $header));
    }

    public function addLink(string $url, ?string $rel = null, ?string $title = null, ?string $type = null)
    {
        $type = $type ? ' type="' . $type . '"' : '';
        $title = $title ? ' title="' . htmlentities($title) . '"' : '';
        $rel = $rel ? ' rel="' . $rel . '"' : '';
        $href = ' href="' . $url . '"';
        $this->addHtmlHeader('<link' . $href . $rel . $title . $type . '/>');
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    protected function getBanner(): Banner
    {
        return new Banner($this->getApplication(), $this->getViewMode(), $this->getContainerMode());
    }

    public function getContainerMode(): string
    {
        return $this->containerMode;
    }

    public function setContainerMode(string $containerMode)
    {
        $this->containerMode = $containerMode;
    }

    /**
     * @return string[]
     */
    public function getHtmlHeaders(): array
    {
        return $this->htmlHeaders;
    }

    /**
     * @param string[] $htmlHeaders
     */
    public function setHtmlHeaders(array $htmlHeaders)
    {
        $this->htmlHeaders = $htmlHeaders;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }

    public function getPathBuilder(): PathBuilder
    {
        return $this->getService(PathBuilder::class);
    }

    public function getTextDirection(): string
    {
        return $this->textDirection;
    }

    public function setTextDirection(string $textDirection)
    {
        $this->textDirection = $textDirection;
    }

    public function getThemePathBuilder(): ThemePathBuilder
    {
        return $this->getService(ThemePathBuilder::class);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return integer
     */
    public function getViewMode(): int
    {
        return $this->viewMode;
    }

    public function setViewMode(int $viewMode)
    {
        $this->viewMode = $viewMode;
    }

    /**
     * @throws \Exception
     * @deprecated Use BaseHeader::render() now
     */
    public function toHtml(): string
    {
        return $this->render();
    }
}
