<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class FooterRenderer extends AbstractFooterRenderer
{
    private ConfigurationConsulter $configurationConsulter;

    private SessionInterface $session;

    private StringUtilities $stringUtilities;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    public function __construct(
        PageConfiguration $pageConfiguration, StringUtilities $stringUtilities,
        ConfigurationConsulter $configurationConsulter, Translator $translator, SessionInterface $session,
        UrlGenerator $urlGenerator
    )
    {
        parent::__construct($pageConfiguration);

        $this->stringUtilities = $stringUtilities;
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    public function render(): string
    {
        $html = [];

        $html[] = $this->getHeader();

        if ($this->getPageConfiguration()->getViewMode() != PageConfiguration::VIEW_MODE_HEADERLESS)
        {
            $html[] = $this->getContainerHeader();
            $html[] = implode(' | ', $this->getLinks());
            $html[] = $this->getContainerFooter();
        }

        $html[] = $this->getFooter();

        return implode(PHP_EOL, $html);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @return string[]
     */
    protected function getLinks(): array
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();

        $institutionUrl = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'institution_url']);
        $institution = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'institution']);

        $administratorEmail = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_email']);
        $administratorWebsite = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_website']);
        $administratorName = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_name']);

        $links = [];

        $links[] = '<a href="' . $institutionUrl . '" target="about:blank">' . $institution . '</a>';

        if (!empty($administratorEmail) && !empty($administratorWebsite))
        {
            $email = $stringUtilities->encryptMailLink($administratorEmail, $administratorName);
            $links[] = $translator->trans(
                'ManagerContactWebsite', ['EMAIL' => $email, 'WEBSITE' => $administratorWebsite],
                StringUtilities::LIBRARIES
            );
        }
        else
        {
            if (!empty($administratorEmail))
            {
                $links[] = $translator->trans('Manager', [], StringUtilities::LIBRARIES) . ': ' .
                    $stringUtilities->encryptMailLink(
                        $administratorEmail, $administratorName
                    );
            }

            if (!empty($administratorWebsite))
            {
                $links[] = $translator->trans('Support', [], StringUtilities::LIBRARIES) . ': <a href="' .
                    $administratorWebsite . '">' . $administratorName . '</a>';
            }
        }

        return $links;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }
}
