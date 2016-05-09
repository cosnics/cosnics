<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Form\SettingsForm;
use Chamilo\Core\Install\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Theme;
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
     * Runs this component and displays its output.
     */
    public function run()
    {
        $form = new SettingsForm($this, $this->get_url());

        if ($form->validate())
        {
            $content = array();

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
     * @return string
     */
    public function getButtons()
    {
        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES),
                new BootstrapGlyph('chevron-left'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SETTINGS))));

        $buttonToolBar->addItem(
            new Button(
                Translation :: get('Next'),
                new BootstrapGlyph('chevron-right'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_INSTALL_PLATFORM)),
                Button :: DISPLAY_ICON_AND_LABEL,
                false,
                'btn-primary'));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    public function getInfo()
    {
        $html = array();
        $html[] = Translation :: get('SettingsComponentInformation');
        $html[] = '<br /><br />';

        $html[] = '<div style="background-image: url(' . Theme :: getInstance()->getImagePath(
            'Chamilo\Configuration',
            'Logo/22') . ')" class="package-list-item-core">';
        $html[] = Translation :: get('CorePackage');
        $html[] = '</div>';

        $html[] = '<div style="background-image: url(' . Theme :: getInstance()->getImagePath(
            'Chamilo\Configuration',
            'Logo/22') . ')" class="package-list-item">';
        $html[] = Translation :: get('AvailablePackage');
        $html[] = '</div>';

        $html[] = '<div style="background-image: url(' . Theme :: getInstance()->getImagePath(
            'Chamilo\Configuration',
            'Logo/22') . ')" class="package-list-item package-list-item-selected">';
        $html[] = Translation :: get('SelectedPackage');
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
