<?php
namespace Chamilo\Core\Home\Renderer\Type\Basic;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Home\Renderer\Type\Basic
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TabHeaderRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var \Chamilo\Core\Home\Service\HomeService
     */
    private $homeService;

    /**
     *
     * @var \Chamilo\Core\Home\Storage\DataClass\Tab
     */
    private $tab;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Tab $tab
     */
    public function __construct(Application $application, HomeService $homeService, Tab $tab)
    {
        $this->application = $application;
        $this->homeService = $homeService;
        $this->tab = $tab;
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
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Service\HomeService
     */
    public function getHomeService()
    {
        return $this->homeService;
    }

    /**
     *
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     */
    public function setHomeService($homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Storage\DataClass\Tab
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     *
     * @param \Chamilo\Core\Home\Storage\DataClass\Tab $tab
     */
    public function setTab(Tab $tab)
    {
        $this->tab = $tab;
    }

    /**
     *
     * @return string
     */
    public function render($isActiveTab)
    {
        $tab = $this->getTab();
        $request = $this->getApplication()->getRequest();
        
        $html = array();
        
        $tab_id = $tab->get_id();
        
        $listItem = array();
        
        $listItem[] = '<li';
        
        if ($isActiveTab)
        {
            $listItem[] = 'class="portal-nav-tab active"';
        }
        else
        {
            $listItem[] = 'class="portal-nav-tab"';
        }
        
        $listItem[] = ' data-tab-id="' . $tab->get_id() . '"';
        $listItem[] = ' data-tab-title="' . $tab->getTitle() . '"';
        $listItem[] = '>';
        
        $html[] = implode(' ', $listItem);
        
        $html[] = '<a class="portal-action-tab-title" href="#">';
        
        $html[] = '<span class="portal-nav-tab-title">' . htmlspecialchars($tab->getTitle()) . '</span>';
        
        $user = $this->getApplication()->getUser();
        $isUser = $user instanceof User;
        $homeAllowed = $isUser && ($this->getHomeService()->isUserHomeAllowed() ||
             ($user->is_platform_admin()) && $this->getHomeService()->isInGeneralMode());
        $isAnonymous = $isUser && $user->is_anonymous_user();
        
        if ($isUser && $homeAllowed && ! $isAnonymous)
        {
            $userHasMultipleTabs = $this->getHomeService()->userHasMultipleTabs($user);
            
            $html[] = '<span class="glyphicon glyphicon-remove portal-action-tab-delete ' .
                 ($userHasMultipleTabs ? 'show' : 'hidden') . '"></span>';
        }
        
        $html[] = '</a>';
        
        $html[] = '</li>';
        
        return implode(PHP_EOL, $html);
    }
}