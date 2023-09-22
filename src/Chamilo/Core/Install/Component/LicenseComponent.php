<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Install\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LicenseComponent extends Manager implements NoAuthenticationSupportInterface
{
    /**
     * @throws \QuickformException
     * @throws \Exception
     */
    public function run()
    {
        $this->checkInstallationAllowed();

        $html = [];

        $html[] = $this->renderHeader();

        $html[] = '<form class="form">';
        $html[] = '<textarea class="form-control" cols="80" rows="30">' .
            implode('', file(realpath(__DIR__ . '/../../../../../LICENSE'))) . '</textarea>';
        $html[] = '</form>';

        $html[] = '<br />';

        $html[] = $this->getButtons();

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
                $this->get_url([self::PARAM_ACTION => self::ACTION_REQUIREMENTS])
            )
        );

        $buttonToolBar->addItem(
            new Button(
                $translator->trans('AgreeAndContinue', [], Manager::CONTEXT), new FontAwesomeGlyph('chevron-right'),
                $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_SETTINGS,
                        self::PARAM_LANGUAGE => $this->getSession()->get(self::PARAM_LANGUAGE)
                    ]
                ), AbstractButton::DISPLAY_ICON_AND_LABEL, null, ['btn-primary']
            )
        );

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    protected function getInfo(): string
    {
        return $this->getTranslator()->trans('LicenseComponentInformation', [], self::CONTEXT);
    }
}
