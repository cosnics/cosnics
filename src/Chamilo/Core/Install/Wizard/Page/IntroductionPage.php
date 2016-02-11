<?php
namespace Chamilo\Core\Install\Wizard\Page;

use Chamilo\Core\Install\Wizard\InstallWizardPage;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: language_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * This form can be used to let the user select the action.
 */
class IntroductionPage extends InstallWizardPage
{

    public function buildForm()
    {
        $this->_formBuilt = true;
        $this->renderer = $this->defaultRenderer();

        $phpVersion = phpversion();

        $this->addElement(
            'html',
            '<div style="position: relative; width: 600px; margin-left: -300px; left: 50%; right: 50%;">');

        if ($phpVersion >= 5.4)
        {
            $html = array();
            $html[] = '<div class="normal-message" style="margin-bottom: 39px; margin-top: 30px;">';
            $html[] = 'From the looks of it, Chamilo is currently not installed on your system.';
            $html[] = '<br />';
            $html[] = '<br />';
            $html[] = 'Please check your database and/or configuration files if you are certain the platform was installed correctly.';
            $html[] = '<br />';
            $html[] = '<br />';
            $html[] = 'If you\'re starting Chamilo for the first time, you may want to install the platform first by clicking the button below. Alternatively, you can read the installation guide, visit chamilo.org for more information or go to the community forum if you need support.';
            $html[] = '</div>';

            $this->addElement('html', implode(PHP_EOL, $html));

            $buttons = array();

            $buttons[] = $this->createElement(
                'style_submit_button',
                $this->getButtonName('next'),
                Translation :: get('Install'));

            $buttons[] = $this->createElement(
                'static',
                null,
                null,
                '<a class="btn btn-default" href="documentation/install.txt" target="_blank"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Read the installation guide</a>');

            $buttons[] = $this->createElement(
                'static',
                null,
                null,
                '<a class="btn btn-default" href="http://www.chamilo.org/" target="_blank"><span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Visit chamilo.org</a>');

            $buttons[] = $this->createElement(
                'static',
                null,
                null,
                '<a class="btn btn-default" href="http://www.chamilo.org/forum/" target="_blank"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Get support</a>');

            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
            $this->renderer->setElementTemplate(
                '<div style="text-align: center; padding: 0px; margin: 0px;">{element}</div>',
                'buttons');

            $this->setDefaultAction($this->getButtonName('next'));
        }
        else
        {
            $html = array();
            $html[] = '<div class="error-message" style="margin-bottom: 39px; margin-top: 30px;">';
            $html[] = 'Your version of PHP is not recent enough to use the Chamilo software.';
            $html[] = '<br />';
            $html[] = '<a href="http://www.php.net">';
            $html[] = 'Please upgrade to PHP version 5.4 or higher';
            $html[] = '</a>';
            $html[] = '</div>';

            $this->addElement('html', implode(PHP_EOL, $html));
        }

        $this->addElement('html', '</div>');
    }

    public function get_title()
    {
        return false;
    }

    public function get_info()
    {
        return false;
    }
}
