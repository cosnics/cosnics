<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Rights\Structure\Service\AuthorizationChecker;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FooterRenderer extends AbstractFooterRenderer
{
    private AuthorizationChecker $authorizationChecker;

    private ConfigurationConsulter $configurationConsulter;

    private SessionUtilities $sessionUtilities;

    private StringUtilities $stringUtilities;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    public function __construct(
        PageConfiguration $pageConfiguration, StringUtilities $stringUtilities,
        ConfigurationConsulter $configurationConsulter, AuthorizationChecker $authorizationChecker,
        Translator $translator, SessionUtilities $sessionUtilities, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($pageConfiguration);

        $this->stringUtilities = $stringUtilities;
        $this->authorizationChecker = $authorizationChecker;
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->sessionUtilities = $sessionUtilities;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \ReflectionException
     */
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

    public function getAuthorizationChecker(): AuthorizationChecker
    {
        return $this->authorizationChecker;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     */
    protected function getLinks(): array
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();

        $showAdministratorData = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'show_administrator_data']);
        $showVersionData = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'show_version_data']);

        $institutionUrl = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'institution_url']);
        $institution = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'institution']);

        $administratorEmail = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_email']);
        $administratorWebsite = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_website']);
        $administratorSurName = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_surname']);
        $administratorFirstName =
            $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_firstname']);

        $administratorName = $administratorSurName . ' ' . $administratorFirstName;

        $links = [];

        $links[] = '<a href="' . $institutionUrl . '" target="about:blank">' . $institution . '</a>';

        if ($showAdministratorData == '1')
        {
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
        }

        if ($showVersionData == '1')
        {
            $links[] = htmlspecialchars($translator->trans('Version', [], StringUtilities::LIBRARIES)) . ' ' .
                $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'version']);
        }

        if ($this->getSessionUtilities()->exists('_uid'))
        {
            $user = new User();
            $user->setId($this->getSessionUtilities()->getUserId());
            $whoisOnlineAuthorized = $this->getAuthorizationChecker()->isAuthorized(
                $user, 'Chamilo\Core\Admin', 'ViewWhoisOnline'
            );

            if ($whoisOnlineAuthorized)
            {
                $whoIsOnlineUrl = $this->getUrlGenerator()->fromParameters([
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_WHOIS_ONLINE
                ]);

                $links[] = '<a href="' . htmlspecialchars($whoIsOnlineUrl) . '">' .
                    $translator->trans('WhoisOnline', [], StringUtilities::LIBRARIES) . '?</a>';
            }
        }

        return $links;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
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
