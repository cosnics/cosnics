<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\Install\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class IntroductionComponent extends Manager implements NoAuthenticationSupportInterface
{

    /**
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function run()
    {
        $this->checkInstallationAllowed();

        $phpVersion = phpversion();

        $html = [];

        $html[] = $this->renderHeader();

        if ($phpVersion >= 5.4)
        {
            $buttonToolBar = new ButtonToolBar();

            $dropDownButton = new DropdownButton(
                $this->getTranslator()->trans('Install', [], Manager::CONTEXT), new FontAwesomeGlyph('check'),
                AbstractButton::DISPLAY_ICON_AND_LABEL, ['btn-primary']
            );

            $buttonToolBar->addItem($dropDownButton);

            foreach ($this->getLanguages() as $languageKey => $languageValue)
            {
                $dropDownButton->addSubButton(
                    new SubButton(
                        $languageValue, null, $this->get_url(
                        [self::PARAM_ACTION => self::ACTION_REQUIREMENTS, self::PARAM_LANGUAGE => $languageKey]
                    )
                    )
                );
            }

            $buttonToolBar->addItem(
                new Button('Read the installation guide', new FontAwesomeGlyph('book'), 'documentation/install.txt')
            );
            $buttonToolBar->addItem(
                new Button('Visit cosnics.org', new FontAwesomeGlyph('globe'), 'http://www.cosnics.org/')
            );
            $buttonToolBar->addItem(
                new Button('Get support', new FontAwesomeGlyph('question-circle'), 'http://www.cosnics.org/')
            );

            $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

            $html[] = $buttonToolbarRenderer->render();
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    protected function getInfo(): string
    {
        $phpVersion = phpversion();

        $html = [];

        if ($phpVersion >= 5.4)
        {
            $html[] = 'From the looks of it, Cosnics is currently not installed on your system.';
            $html[] = '<br />';
            $html[] = '<br />';
            $html[] =
                'Please check your database and/or configuration files if you are certain the platform was installed correctly.';
            $html[] = '<br />';
            $html[] = '<br />';
            $html[] =
                'If you\'re starting Cosnics for the first time, you may want to install the platform first by clicking the button below. Alternatively, you can read the installation guide, visit chamilo.org for more information or go to the community forum if you need support.';
        }
        else
        {
            $html[] = '<div class="error-message" style="margin-bottom: 39px; margin-top: 30px;">';
            $html[] = 'Your version of PHP is not recent enough to use the Cosnics software.';
            $html[] = '<br />';
            $html[] = '<a href="http://www.php.net">';
            $html[] = 'Please upgrade to PHP version 5.4 or higher';
            $html[] = '</a>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
