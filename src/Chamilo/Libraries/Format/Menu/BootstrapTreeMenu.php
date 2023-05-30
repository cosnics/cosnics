<?php
namespace Chamilo\Libraries\Format\Menu;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
abstract class BootstrapTreeMenu
{
    public const NODE_PLACEHOLDER = '__NODE__';

    /**
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     * @var string
     */
    private $menuName;

    /**
     * @var string
     */
    private $treeMenuUrl;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string $treeMenuUrl
     * @param string $menuName
     */
    public function __construct(Application $application, $treeMenuUrl, $menuName = 'bootstrap-tree-menu')
    {
        $this->application = $application;
        $this->treeMenuUrl = $treeMenuUrl;
        $this->menuName = $menuName;
    }

    /**
     * @return string
     * @todo Remove the revealNode and expandNode functionality due to issues with own node-ids generated
     *       by plugin
     */
    public function render()
    {
        $html = [];

        $html[] = '<div id="' . $this->getMenuName() . '">';
        $html[] = '</div>';

        $html[] = "<script>
            $(function()
            {
                $(document).ready(function()
                {
                    $('#" . $this->getMenuName() . "').treeview({
                        enableLinks : true,
                        expandIcon: 'inline-glyph fas fa-chevron-right fa-fw',
                        collapseIcon: 'inline-glyph fas fa-chevron-down fa-fw',
                        emptyIcon: 'treeview-empty-icon',
                        color: '#428bca',
                        showBorder: false,
                        checkedIcon: 'inline-glyph fas fa-check fa-fw',
                        data: " . json_encode($this->getNodes()) . '
                    })
                });
            });
        </script>';

        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        /**
         * @var \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
         */
        $resourceManager = $container->get(ResourceManager::class);

        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder = $container->get(WebPathBuilder::class);

        $html[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getJavascriptPath(StringUtilities::LIBRARIES) .
            'Plugin/Bootstrap/treeview/dist/bootstrap-treeview.min.js'
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return int
     */
    abstract public function getCurrentNodeId();

    /**
     * @return string
     */
    public function getMenuName()
    {
        return $this->menuName;
    }

    /**
     * @param int $nodeIdentifier
     *
     * @return string
     */
    public function getNodeUrl($nodeIdentifier)
    {
        return str_replace(self::NODE_PLACEHOLDER, $nodeIdentifier, $this->getTreeMenuUrl());
    }

    /**
     * @return string[]
     */
    abstract public function getNodes();

    /**
     * @return string
     */
    public function getTreeMenuUrl()
    {
        return $this->treeMenuUrl;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param string $menuName
     */
    public function setMenuName($menuName)
    {
        $this->menuName = $menuName;
    }

    /**
     * @param string $treeMenuUrl
     */
    public function setTreeMenuUrl($treeMenuUrl)
    {
        $this->treeMenuUrl = $treeMenuUrl;
    }
}