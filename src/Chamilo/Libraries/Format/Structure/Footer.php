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

    /**
     *
     * @param integer $viewMode
     * @param string $containerMode
     */
    public function __construct($viewMode = Page::VIEW_MODE_FULL, $containerMode = 'container-fluid')
    {
        parent::__construct($viewMode, $containerMode);
        $this->initializeContainer();
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Structure\BaseFooter::render()
     */
    public function render()
    {
        $html = array();

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
     *
     * @return string[]
     */
    protected function getLinks()
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

        $links = array();

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
