<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Configuration\Configuration;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Footer
{
    use DependencyInjectionContainerTrait;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var integer
     */
    private $viewMode;

    /**
     *
     * @var string
     */
    private $containerMode;

    /**
     *
     * @param integer $viewMode
     */
    public function __construct($viewMode = Page :: VIEW_MODE_FULL, $containerMode = 'container-fluid')
    {
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
        
        $this->initializeContainer();
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return integer
     */
    public function getViewMode()
    {
        return $this->viewMode;
    }

    /**
     *
     * @param integer $viewMode
     */
    public function setViewMode($viewMode)
    {
        $this->viewMode = $viewMode;
    }

    /**
     *
     * @return string
     */
    public function getContainerMode()
    {
        return $this->containerMode;
    }

    /**
     *
     * @param string $containerMode
     */
    public function setContainerMode($containerMode)
    {
        $this->containerMode = $containerMode;
    }

    /**
     * Returns the HTML code for the footer
     */
    public function toHtml()
    {
        $html = array();
        
        $html[] = '</div> <!-- end of .container-fluid" -->';
        
        if ($this->getViewMode() != Page::VIEW_MODE_HEADERLESS)
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
            
            $html[] = '<footer class="chamilo-footer">';
            $html[] = '<div class="' . $this->getContainerMode() . '">';
            $html[] = '<div class="row footer">';
            $html[] = '<div class="col-xs-12">';
            
            $links = array();
            
            $links[] = '<a href="' . $institutionUrl . '" target="about:blank">' . $institution . '</a>';
            
            if ($showAdministratorData == '1')
            {
                if (! empty($administratorEmail) && ! empty($administratorWebsite))
                {
                    $email = $stringUtilities->encryptMailLink($administratorEmail, $administratorName);
                    $links[] = Translation::get(
                        'ManagerContactWebsite', 
                        array('EMAIL' => $email, 'WEBSITE' => $administratorWebsite));
                }
                else
                {
                    if (! empty($administratorEmail))
                    {
                        $links[] = Translation::get('Manager') . ': ' . $stringUtilities->encryptMailLink(
                            $administratorEmail, 
                            $administratorName);
                    }
                    
                    if (! empty($administratorWebsite))
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
                    $user, 
                    'Chamilo\Core\Admin', 
                    'ViewWhoisOnline');
                
                if ($whoisOnlineAuthorized)
                {
                    $redirect = new Redirect(
                        array(
                            Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(), 
                            Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_WHOIS_ONLINE));
                    
                    $links[] = '<a href="' . htmlspecialchars($redirect->getUrl()) . '">' . Translation::get(
                        'WhoisOnline') . '?</a>';
                }
            }
            
            $html[] = implode(' | ', $links);
            
            $html[] = '&nbsp;&copy;&nbsp;' . date('Y');
            
            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '</div> <!-- end of .container-fluid" -->';
            $html[] = '</footer>';
        }
        
        $html[] = '</body>';
        $html[] = '</html>';
        
        return implode(PHP_EOL, $html);
    }
}
