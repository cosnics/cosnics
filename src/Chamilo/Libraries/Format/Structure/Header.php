<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Cache\Assetic\JavascriptCacheService;
use Chamilo\Libraries\Cache\Assetic\StylesheetCacheService;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Header
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

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
     * @param string $languageCode
     * @param string $textDirection
     */
    public function __construct($viewMode = Page :: VIEW_MODE_FULL, $containerMode = 'container-fluid', $languageCode = 'en', $textDirection = 'ltr')
    {
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
        $this->languageCode = $languageCode;
        $this->textDirection = $textDirection;

        $this->htmlHeaders = array();
        $this->addDefaultHeaders();
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
     * Adds some default headers to the output
     */
    private function addDefaultHeaders()
    {
        $server_type = \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'server_type');
        $theme = Theme :: getInstance()->getTheme();

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = 'Chamilo\Libraries\Ajax';
        $parameters[Application :: PARAM_ACTION] = 'resource';
        $parameters[ResourceUtilities :: PARAM_THEME] = $theme;
        $parameters[ResourceUtilities :: PARAM_SERVER_TYPE] = $server_type;

        $this->addHtmlHeader('<meta charset="utf-8">');
        $this->addHtmlHeader('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
        $this->addHtmlHeader('<meta name="viewport" content="width=device-width, initial-scale=1">');
        $this->addHtmlHeader('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');

        $stylesheetCacheService = new StylesheetCacheService(Path :: getInstance(), Theme :: getInstance());
        $cssModified = $stylesheetCacheService->getLastModificationTime();
        $cssModified = $cssModified ? $cssModified : time();

        $parameters[ResourceUtilities :: PARAM_TYPE] = 'css';
        $parameters['modified'] = $cssModified;

        $this->addCssFile(Path :: getInstance()->getBasePath(true) . 'index.php?' . http_build_query($parameters));

        $this->addLink(Path :: getInstance()->getBasePath(true) . 'index.php', 'top');
        $this->addLink(
            Theme :: getInstance()->getCommonImagePath('Favicon', 'ico'),
            'shortcut icon',
            null,
            'image/x-icon');

        $this->addHtmlHeader(
            '<script type="text/javascript">var rootWebPath="' . Path :: getInstance()->getBasePath(true) . '";</script>');

        $javascriptCacheService = new JavascriptCacheService(Path :: getInstance());
        $javascriptModified = $javascriptCacheService->getLastModificationTime();
        $javascriptModified = $javascriptModified ? $javascriptModified : time();

        $parameters[ResourceUtilities :: PARAM_TYPE] = 'javascript';
        $parameters['modified'] = $javascriptModified;
        $this->addJavascriptFile(
            Path :: getInstance()->getBasePath(true) . 'index.php?' . http_build_query($parameters));

        $pageTitle = \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'institution') . ' - ' .
             \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'site_name');

        $this->addHtmlHeader('<title>' . $pageTitle . '</title>');

        $this->addGoogleAnalyticsTracking();
    }

    /**
     * Adds the google analytics tracking to the header if configured
     */
    protected function addGoogleAnalyticsTracking()
    {
        if(!\Chamilo\Configuration\Configuration::getInstance()->is_available())
        {
            return;
        }

        $googleAnalyticsTrackingId = \Chamilo\Configuration\Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'google_analytics_tracking_id')
            );

        if(!empty($googleAnalyticsTrackingId))
        {
            $html = array();

            $html[] = '<script>';
            $html[] = '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){';
            $html[] = '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),';
            $html[] = 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)';
            $html[] = '})(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');';
            $html[] = '';
            $html[] = 'ga(\'create\', \'' . $googleAnalyticsTrackingId . '\', \'auto\');';
            $html[] = 'ga(\'send\', \'pageview\', location.pathname + location.search);';
            $html[] = '';
            $html[] = '</script>';

            $this->addHtmlHeader(implode(PHP_EOL, $html));
        }
    }

    /**
     * Adds a html header
     */
    public function addHtmlHeader($html_header)
    {
        $this->htmlHeaders[] = $html_header;
    }

    /**
     * Adds a css file
     */
    public function addCssFile($file, $media = 'screen,projection')
    {
        $header = '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $file . '" />';
        $this->addHtmlHeader($header);
    }

    /**
     * Adds a javascript file
     */
    public function addJavascriptFile($file)
    {
        $header[] = '<script type="text/javascript" src="' . $file . '"></script>';
        $this->addHtmlHeader(implode(' ', $header));
    }

    /**
     * Adds a link
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

    /**
     * Creates the HTML output for the header.
     * This function will send all http headers to the browser and return the
     * head-tag of the html document
     */
    public function toHtml()
    {
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

        if ($this->getViewMode() != Page :: VIEW_MODE_HEADERLESS)
        {
            $banner = new Banner($this->getApplication(), $this->getViewMode(), $this->getContainerMode());

            $html[] = $banner->render();
        }

        $classes = $this->getContainerMode();

        if ($this->getViewMode() == Page :: VIEW_MODE_HEADERLESS)
        {
            $classes .= ' container-headerless';
        }

        $html[] = '<div class="' . $classes . '">';

        return implode(PHP_EOL, $html);
    }
}
