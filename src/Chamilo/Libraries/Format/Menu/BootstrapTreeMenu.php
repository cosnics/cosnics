<?php
namespace Chamilo\Libraries\Format\Menu;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class BootstrapTreeMenu
{
    const NODE_PLACEHOLDER = '__NODE__';

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var string
     */
    private $menuName;

    /**
     *
     * @var string
     */
    private $treeMenuUrl;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string $treeMenuUrl
     * @param string $menuName
     */
    public function __construct(
        Application $application,
        $treeMenuUrl, $menuName = 'bootstrap-tree-menu'
    )
    {
        $this->application = $application;
        $this->treeMenuUrl = $treeMenuUrl;
        $this->menuName = $menuName;
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
     * @return string
     */
    public function getTreeMenuUrl()
    {
        return $this->treeMenuUrl;
    }

    /**
     *
     * @param string $treeMenuUrl
     */
    public function setTreeMenuUrl($treeMenuUrl)
    {
        $this->treeMenuUrl = $treeMenuUrl;
    }

    /**
     *
     * @return string
     */
    public function getMenuName()
    {
        return $this->menuName;
    }

    /**
     *
     * @param string $menuName
     */
    public function setMenuName($menuName)
    {
        $this->menuName = $menuName;
    }

    /**
     *
     * @param int $nodeIdentifier
     *
     * @return string
     */
    public function getNodeUrl($nodeIdentifier)
    {
        return str_replace(self::NODE_PLACEHOLDER, $nodeIdentifier, $this->getTreeMenuUrl());
    }

    /**
     *
     * @return string[]
     */
    abstract function getNodes();

    /**
     *
     * @return integer
     */
    abstract function getCurrentNodeId();

    /**
     *
     * @return string
     */
    public function render()
    {
        $currentNodeId = $this->getCurrentNodeId();

        $html = array();

        $html[] = '<div id="' . $this->getMenuName() . '">';
        $html[] = '</div>';

        $html[] = "<script>
            $(function()
            {
                $(document).ready(function()
                {
                    $('#" . $this->getMenuName() . "').treeview({
                        enableLinks : true,
                        expandIcon: 'glyphicon glyphicon-chevron-right',
                        collapseIcon: 'glyphicon glyphicon-chevron-down',
                        color: '#428bca',
                        showBorder: false,
                        checkedIcon: 'glyphicon glyphicon-ok',
                        data: " . json_encode($this->getNodes()) . "
                    });

                    $('#" . $this->getMenuName() . "').treeview(
                            'revealNode',
                            [ " . $currentNodeId . ",
                                { silent: true }
                            ]
                    );
                    
                    $('#" . $this->getMenuName() . "').treeview(
                            'expandNode',
                            [ " . $currentNodeId . ",
                                { silent: false }
                            ]
                    );
                });
            });
        </script>";

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Utilities::COMMON_LIBRARIES, true) .
            'Plugin/Bootstrap/treeview/dist/bootstrap-treeview.min.js'
        );

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Utilities::COMMON_LIBRARIES, true) .
            'Plugin/Bootstrap/treeview/dist/bootstrap-treeview.min.css'
        );

        return implode(PHP_EOL, $html);
    }
}