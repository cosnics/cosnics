<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HeaderRenderer extends AbstractHeaderRenderer
{
    private ConfigurationConsulter $configurationConsulter;

    private FileConfigurationLocator $fileConfigurationLocator;

    public function __construct(
        PageConfiguration $pageConfiguration, PathBuilder $pathBuilder, ThemePathBuilder $themePathBuilder,
        ConfigurationConsulter $configurationConsulter, FileConfigurationLocator $fileConfigurationLocator
    )
    {
        parent::__construct($pageConfiguration, $pathBuilder, $themePathBuilder);

        $this->configurationConsulter = $configurationConsulter;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
    }

    /**
     * @throws \Exception
     */
    protected function addDefaultHeaders()
    {
        parent::addDefaultHeaders();
        $this->addGoogleAnalyticsTracking();
    }

    /**
     * Adds the google analytics tracking to the header if configured
     * @throws \Exception
     */
    protected function addGoogleAnalyticsTracking()
    {
        if (!$this->getFileConfigurationLocator()->isAvailable())
        {
            return;
        }

        $googleAnalyticsTrackingId = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Core\Admin', 'google_analytics_tracking_id']
        );

        if (!empty($googleAnalyticsTrackingId))
        {
            $html = [];
            $pageConfiguration = $this->getPageConfiguration();

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

            $pageConfiguration->addHtmlHeader(implode(PHP_EOL, $html));
        }
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getFileConfigurationLocator(): FileConfigurationLocator
    {
        return $this->fileConfigurationLocator;
    }
}
