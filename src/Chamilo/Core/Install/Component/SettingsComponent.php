<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Form\SettingsForm;
use Chamilo\Core\Install\Manager;
use Chamilo\Core\Install\Service\SettingsOverviewRenderer;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Install\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SettingsComponent extends Manager implements NoAuthenticationSupportInterface
{

    private SettingsForm $settingsForm;

    /**
     * Runs this component and displays its output.
     *
     * @throws \QuickformException
     * @throws \Exception
     */
    public function run()
    {
        $this->checkInstallationAllowed();

        $form = $this->getSettingsForm();

        if ($form->validate())
        {
            $settingsValues = $form->exportValues();
            $this->getSession()->set(self::PARAM_SETTINGS, serialize($settingsValues));

            $wizardHeader = $this->getWizardHeader();
            $wizardHeader->setSelectedStepIndex(array_search(self::ACTION_OVERVIEW, $this->getWizardHeaderActions()));

            $content = [];

            $content[] = $this->getSettingsOverviewRenderer()->render($settingsValues);
            $content[] = $this->getButtons();

            $content = implode(PHP_EOL, $content);
        }
        else
        {
            $content = $form->render();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $content;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function getButtons(): string
    {
        $translator = $this->getTranslator();
        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                $translator->trans('Previous', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('chevron-left'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_SETTINGS])
            )
        );

        $buttonToolBar->addItem(
            new Button(
                $translator->trans('Install', [], Manager::CONTEXT), new FontAwesomeGlyph('check'), $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_INSTALL_PLATFORM,
                    self::PARAM_LANGUAGE => $this->getSession()->get(self::PARAM_LANGUAGE)
                ]
            ), AbstractButton::DISPLAY_ICON_AND_LABEL, null, ['btn-success']
            )
        );

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    /**
     * @throws \QuickformException
     */
    protected function getInfo(): string
    {
        $translator = $this->getTranslator();
        $html = [];

        if ($this->getSettingsForm()->validate())
        {
            $html[] = $translator->trans('SettingsOverviewInformation', [], Manager::CONTEXT);
        }
        else
        {
            $html[] = $translator->trans('SettingsComponentInformation', [], Manager::CONTEXT);
            $html[] = '<br /><br />';

            $glyph = new NamespaceIdentGlyph('Chamilo\Configuration', true, false, true);

            $html[] = '<a class="btn btn-default" disabled="disabled">';
            $html[] = $glyph->render();
            $html[] = $translator->trans('CorePackage', [], Manager::CONTEXT);
            $html[] = '</a>';

            $glyph = new NamespaceIdentGlyph('Chamilo\Configuration', true, false, false);

            $html[] = '<a class="btn btn-default">';
            $html[] = $glyph->render();
            $html[] = $translator->trans('AvailablePackage', [], Manager::CONTEXT);
            $html[] = '</a>';

            $html[] = '<a class="btn btn-success">';
            $html[] = $glyph->render();
            $html[] = $translator->trans('SelectedPackage', [], Manager::CONTEXT);
            $html[] = '</a>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function getSettingsForm(): SettingsForm
    {
        if (!isset($this->settingsForm))
        {
            $this->settingsForm = new SettingsForm(
                $this, $this->get_url([self::PARAM_LANGUAGE => $this->getSession()->get(self::PARAM_LANGUAGE)])
            );
        }

        return $this->settingsForm;
    }

    public function getSettingsOverviewRenderer(): SettingsOverviewRenderer
    {
        return $this->getService(SettingsOverviewRenderer::class);
    }
}
