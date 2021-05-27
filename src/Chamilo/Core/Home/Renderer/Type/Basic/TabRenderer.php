<?php
namespace Chamilo\Core\Home\Renderer\Type\Basic;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Home\Renderer\Type\Basic
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TabRenderer
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
        
        $html = [];
        
        $html[] = '<div class="row portal-tab ' . ($isActiveTab ? 'show' : 'hidden') . '" data-element-id="' .
             $tab->getId() . '">';
        
        $columns = $this->getHomeService()->getElements(
            $this->getApplication()->getUser(), 
            Column::class,
            $tab->getId());
        
        foreach ($columns as $column)
        {
            $columnRenderer = new ColumnRenderer($this->getApplication(), $this->getHomeService(), $column);
            $html[] = $columnRenderer->render();
        }
        
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}