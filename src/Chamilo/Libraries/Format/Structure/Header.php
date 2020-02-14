<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Header extends BaseHeader
{

    /**
     * Adds some default headers to the output
     */
    protected function addDefaultHeaders()
    {
        parent::addDefaultHeaders();
//        $this->addGoogleAnalyticsTracking(); REMOVE THIS FOR GDPR COMPLIANCE FOR NOW
    }

    /**
     * Adds the google analytics tracking to the header if configured
     */
    protected function addGoogleAnalyticsTracking()
    {
        $fileConfigurationLocator = new FileConfigurationLocator(new PathBuilder(ClassnameUtilities::getInstance()));

        if (! $fileConfigurationLocator->isAvailable())
        {
            return;
        }

        $googleAnalyticsTrackingId = \Chamilo\Configuration\Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'google_analytics_tracking_id'));

        if (! empty($googleAnalyticsTrackingId))
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
}
