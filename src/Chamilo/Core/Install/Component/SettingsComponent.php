<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Form\SettingsForm;
use Chamilo\Core\Install\Manager;
use Chamilo\Core\Install\SettingsOverview;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Install\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SettingsComponent extends Manager implements NoAuthenticationSupport
{

    /**
     *
     * @var \Chamilo\Core\Install\Form\SettingsForm
     */
    private $settingsForm;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkInstallationAllowed();

        $form = $this->getSettingsForm();

        if ($form->validate())
        {
            $settingsValues = $form->exportValues();
            Session::register(self::PARAM_SETTINGS, serialize($settingsValues));

            $settingsDisplayer = new SettingsOverview($settingsValues);
            $wizardHeader = $this->getWizardHeader();
            $wizardHeader->setSelectedStepIndex(array_search(self::ACTION_OVERVIEW, $this->getWizardHeaderActions()));

            $content = array();

            $content[] = $settingsDisplayer->render();
            $content[] = $this->getButtons();

            $content = implode(PHP_EOL, $content);
        }
        else
        {
            $content = $form->toHtml();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $content;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Core\Install\Form\SettingsForm
     */
    public function getSettingsForm()
    {
        if (! isset($this->settingsForm))
        {
            $this->settingsForm = new SettingsForm($this, $this->get_url(array(self::PARAM_LANGUAGE => Session::retrieve(self::PARAM_LANGUAGE))));
        }

        return $this->settingsForm;
    }

    /**
     *
     * @return string
     */
    public function getButtons()
    {
        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                Translation::get('Previous', null, Utilities::COMMON_LIBRARIES),
                new BootstrapGlyph('chevron-left'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_SETTINGS))));

        $buttonToolBar->addItem(
            new Button(
                Translation::get('Install'),
                new BootstrapGlyph('ok'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_INSTALL_PLATFORM, self::PARAM_LANGUAGE => Session::retrieve(self::PARAM_LANGUAGE))),
                Button::DISPLAY_ICON_AND_LABEL,
                false,
                'btn-success'));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    public function getInfo()
    {
        $html = array();

        if ($this->getSettingsForm()->validate())
        {
            $html[] = Translation::get('SettingsOverviewInformation');
        }
        else
        {
            $html[] = Translation::get('SettingsComponentInformation');
            $html[] = '<br /><br />';

            $html[] = '<a class="btn btn-default" disabled="disabled"><img src="' . Theme::getInstance()->getImagePath(
                'Chamilo\Configuration',
                'Logo/22Na') . '"> ';
            $html[] = Translation::get('CorePackage');
            $html[] = '</a>';

            $html[] = '<a class="btn btn-default"><img src="' . Theme::getInstance()->getImagePath(
                'Chamilo\Configuration',
                'Logo/22') . '"> ';
            $html[] = Translation::get('AvailablePackage');
            $html[] = '</a>';

            $html[] = '<a class="btn btn-success"><img src="' . Theme::getInstance()->getImagePath(
                'Chamilo\Configuration',
                'Logo/22') . '"> ';
            $html[] = Translation::get('SelectedPackage');
            $html[] = '</a>';
        }

        return implode(PHP_EOL, $html);
    }
}
