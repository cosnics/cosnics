<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Footer extends BaseFooter
{
    use DependencyInjectionContainerTrait;

    public function __construct(int $viewMode = Page::VIEW_MODE_FULL, string $containerMode = 'container-fluid')
    {
        parent::__construct($viewMode, $containerMode);
        $this->initializeContainer();
    }

    public function render(): string
    {
        $html = [];

        $html[] = $this->getHeader();

        if ($this->getViewMode() != Page::VIEW_MODE_HEADERLESS)
        {

            $html[] = $this->getContainerHeader();
            $html[] = implode(' | ', $this->getLinks());
            $html[] = $this->getContainerFooter();
        }

        $html[] = $this->getFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     */
    protected function getLinks(): array
    {
        $showAdministratorData = Configuration::get('Chamilo\Core\Admin', 'show_administrator_data');
        $showVersionData = Configuration::get('Chamilo\Core\Admin', 'show_version_data');

        $institutionUrl = Configuration::get('Chamilo\Core\Admin', 'institution_url');
        $institution = Configuration::get('Chamilo\Core\Admin', 'institution');

        $administratorEmail = Configuration::get('Chamilo\Core\Admin', 'administrator_email');
        $administratorWebsite = Configuration::get('Chamilo\Core\Admin', 'administrator_website');
        $administratorSurName = Configuration::get('Chamilo\Core\Admin', 'administrator_surname');
        $administratorFirstName = Configuration::get('Chamilo\Core\Admin', 'administrator_firstname');

        $administratorName = $administratorSurName . ' ' . $administratorFirstName;

        $stringUtilities = StringUtilities::getInstance();

        $links = [];

        $links[] = '<a href="' . $institutionUrl . '" target="about:blank">' . $institution . '</a>';

        if ($showAdministratorData == '1')
        {
            if (!empty($administratorEmail) && !empty($administratorWebsite))
            {
                $email = $stringUtilities->encryptMailLink($administratorEmail, $administratorName);
                $links[] = Translation::get(
                    'ManagerContactWebsite', array('EMAIL' => $email, 'WEBSITE' => $administratorWebsite)
                );
            }
            else
            {
                if (!empty($administratorEmail))
                {
                    $links[] = Translation::get('Manager') . ': ' . $stringUtilities->encryptMailLink(
                            $administratorEmail, $administratorName
                        );
                }

                if (!empty($administratorWebsite))
                {
                    $links[] = Translation::get('Support') . ': <a href="' . $administratorWebsite . '">' .
                        $administratorName . '</a>';
                }
            }
        }

        if ($showVersionData == '1')
        {
            $links[] = htmlspecialchars(Translation::get('Version')) . ' ' .
                Configuration::get('Chamilo\Core\Admin', 'version');
        }

        if (key_exists('_uid', $_SESSION))
        {
            $user = new User();
            $user->setId(Session::get_user_id());
            $whoisOnlineAuthorized = $this->getAuthorizationChecker()->isAuthorized(
                $user, 'Chamilo\Core\Admin', 'ViewWhoisOnline'
            );

            if ($whoisOnlineAuthorized)
            {
                $redirect = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => Manager::context(),
                        Application::PARAM_ACTION => Manager::ACTION_WHOIS_ONLINE
                    )
                );

                $links[] =
                    '<a href="' . htmlspecialchars($redirect->getUrl()) . '">' . Translation::get('WhoisOnline') .
                    '?</a>';
            }
        }

        return $links;
    }
}
