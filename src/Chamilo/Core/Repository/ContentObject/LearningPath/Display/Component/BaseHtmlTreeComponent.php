<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeRenderer;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\NodeActionGenerator;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

abstract class BaseHtmlTreeComponent extends Manager implements DelegateComponent
{

    private $learning_path_menu;

    public function get_prerequisites_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id =
            ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;

        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_BUILD_PREREQUISITES,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item
            )
        );
    }

    public function run()
    {
        $learning_path = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail::getInstance();

        if (!$learning_path)
        {
            return $this->display_error_page(Translation::get('NoObjectSelected'));
        }

//        $this->learning_path_menu = new LearningPathTreeRenderer(
//            $this->getLearningPathTree(), $this,
//            $this->getLearningPathTrackingService(),
//            $this->getAutomaticNumberingService(),
//            $this->get_parent()->get_learning_path_tree_menu_url(), 'learning-path-menu'
//        );

        $parentAndCurrentNodes = $this->getCurrentLearningPathTreeNode()->getParentNodes();
        $parentAndCurrentNodes[] = $this->getCurrentLearningPathTreeNode();

        $automaticNumberingService = $this->getAutomaticNumberingService();

        foreach ($parentAndCurrentNodes as $parentNode)
        {
            $title = $automaticNumberingService->getAutomaticNumberedTitleForLearningPathTreeNode($parentNode);
            $url = $this->getLearningPathTreeNodeNavigationUrl($parentNode);

            $trail->add(new Breadcrumb($url, $title));
        }

        return $this->build();
    }

    abstract function build();

    /**
     */
    public function render_header()
    {
        $isFullScreen = $this->getRequest()->query->get(self::PARAM_FULL_SCREEN, false);
        $isMenuHidden = Session::retrieve('learningPathMenuIsHidden');

        if ($isFullScreen)
        {
            Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        }

        $html = array();

        $html[] = parent::render_header();

        $html[] = '<div class="learning-path-display">';
        $html[] = '<iframe class="learning-path-display-full-screen" src="about:blank"></iframe>';

        $html[] = $this->get_navigation_bar();

        $html[] = '<div class="row">';

        // Menu

        $classes = array('col-xs-12', 'col-sm-3', 'col-lg-3', 'learning-path-tree-menu-container');

        if ($isMenuHidden == 'true')
        {
            $classes[] = 'learning-path-tree-menu-container-hidden';
        }
        else
        {
            $classes[] = 'learning-path-tree-menu-container-visible';
        }

        $html[] = '<div class="' . implode(' ', $classes) . '">';

        $html[] = '<h3>';
        $html[] = Translation::get('LearningPathNavigationMenu');
        $html[] = '</h3>';

        $html[] = '<div class="learning-path-tree-menu">';

        $learningPathHtmlTreePath = Path::getInstance()->getResourcesPath(
                "Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Display"
            ) . '/Templates/LearningPathHtmlTree.html';
        $learningPathHtmlTree = file_get_contents($learningPathHtmlTreePath);

        $reportingActions = array(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_REPORTING,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_ASSESSMENT_RESULT
        );

        $inReportingMode = in_array($this->get_action(), $reportingActions);

        $parameters = array(
            'fetchTreeNodesAjaxUrl' => $this->get_url(array(self::PARAM_ACTION => self::ACTION_AJAX)),
            'moveTreeNodeAjaxUrl' => $this->get_url(array(
                self::PARAM_ACTION => self::ACTION_AJAX,
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION
                    => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_MOVE_LEARNING_PATH_TREE_NODE_COMPONENT
                )
            ),
            'canEditLearningPathTree' =>
                $this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()) ? 'true' : 'false',
            'inReportingMode' => $inReportingMode ? 'true': 'false',
	        'treeData' => $this->getBootstrapTreeData()
        );

        foreach($parameters as $parameter => $value)
        {
            $learningPathHtmlTree = str_replace('{{ ' . $parameter . ' }}', $value, $learningPathHtmlTree);
        }

//        $html[] = $this->learning_path_menu->render();
        $html[] = $learningPathHtmlTree;

        $html[] = '</div>';

        if($this->get_action() == self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT)
        {
            $learningPathTrackingService = $this->getLearningPathTrackingService();
            $progress =
                $learningPathTrackingService->getLearningPathProgress(
                    $this->get_root_content_object(), $this->getUser()
                );

            $progressBarRenderer = new ProgressBarRenderer();

            $html[] = '<a href="' . $this->get_url(array(self::PARAM_ACTION => self::ACTION_REPORTING)) . '">';
            $html[] = $progressBarRenderer->render($progress, ProgressBarRenderer::MODE_DEFAULT, 0);
            $html[] = '</a>';
        }

        $html[] = '</div>';

        // Content

        $classes = array('col-xs-12', 'col-sm-9', 'col-lg-9', 'learning-path-content');

        if ($isMenuHidden == 'true')
        {
            $classes[] = 'learning-path-content-full-screen';
        }

        $html[] = '<div class="' . implode(' ', $classes) . '">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\SubManager::render_footer()
     */
    public function render_footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::package(), true) . 'LearningPathMenu.js'
        );
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Utilities::COMMON_LIBRARIES, true) .
            'Plugin/Jquery/jquery.fullscreen.min.js'
        );

        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_learning_path_menu()
    {
        return $this->learning_path_menu;
    }

    /**
     * Retrieves the navigation menu for the learning path
     */
    private function get_navigation_bar()
    {
        $currentLearningPathTreeNode = $this->getCurrentLearningPathTreeNode();

        $html = array();

        $html[] = '<div class="navbar-learning-path">';

        $html[] = '<div class="navbar-learning-path-actions">';
        $html[] = '<span class="learning-path-action-menu">';

        $previous_node = $currentLearningPathTreeNode->getPreviousNode();

        if ($previous_node instanceof LearningPathTreeNode)
        {
            $previous_url = $this->getLearningPathTreeNodeNavigationUrl($previous_node);
            $label = Translation::get('Previous');

            $html[] = '<a id="learning-path-navigate-left" href="' . $previous_url .
                '"><span class="glyphicon glyphicon-arrow-left" alt="' . $label . '" title="' . $label .
                '"></span></a>';
        }
        else
        {
            $label = Translation::get('PreviousNa');

            $html[] =
                '<span class="glyphicon glyphicon-arrow-left disabled" alt="' . $label . '" title="' . $label .
                '"></span>';
        }

        $isMenuHidden = Session::retrieve('learningPathMenuIsHidden');

        $html[] = '<span class="glyphicon glyphicon-list-alt learning-path-action-menu-show' .
            ($isMenuHidden != 'true' ? ' hidden' : '') . '"></span>';
        $html[] = '<span class="glyphicon glyphicon-list-alt learning-path-action-menu-hide' .
            ($isMenuHidden == 'true' ? ' hidden' : '') . '"></span>';
        $html[] = '&nbsp;';
        $html[] = '<span class="glyphicon glyphicon-fullscreen learning-path-action-fullscreen"></span>';
        $html[] = '</span>';

        $next_node = $currentLearningPathTreeNode->getNextNode();

        if ($next_node instanceof LearningPathTreeNode)
        {
            $next_url = $this->getLearningPathTreeNodeNavigationUrl($next_node);
            $label = Translation::get('Next');

            $html[] = '<a id="learning-path-navigate-right" href="' . $next_url .
                '"><span class="glyphicon glyphicon-arrow-right" alt="' . $label . '" title="' . $label .
                '"></span></a>';
        }
        else
        {
            $label = Translation::get('NextNa');

            $html[] =
                '<span class="glyphicon glyphicon-arrow-right disabled" alt="' . $label . '" title="' . $label .
                '"></span>';
        }

        $html[] = '</div>';

        $html[] = '<div class="navbar-learning-path-progress">';
        $html[] = $this->get_progress_bar();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the progress bar for the learning path
     *
     * @return string
     */
    private function get_progress_bar()
    {
        $progress = $this->getLearningPathTrackingService()->getLearningPathProgress(
            $this->get_root_content_object(), $this->getUser()
        );

        return $this->render_progress_bar($progress);
    }

    /**
     *
     * @param integer $percent
     * @param integer $step
     *
     * @return string
     */
    private function render_progress_bar($percent, $step = 2)
    {
        $displayPercent = round($percent);

        $html[] = '<div class="progress">';
        $html[] =
            '<div class="progress-bar progress-bar-striped progress-bar-info active" role="progressbar" aria-valuenow="' .
            $displayPercent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $displayPercent .
            '%; min-width: 2em;">';
        // $html[] = $displayPercent . '%';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function getBootstrapTreeData()
    {
        $learningPathTree = $this->getLearningPathTree();

        $learningPathTreeJSONMapper = new LearningPathTreeJSONMapper(
            $learningPathTree, $this->getUser(),
            $this->getLearningPathTrackingService(),
            $this->getAutomaticNumberingService(),
            new NodeActionGenerator(Translation::getInstance(), $this->get_parameters()),
            $this->get_application()->get_learning_path_tree_menu_url(),
            $this->getCurrentLearningPathTreeNode(),
            $this->get_application()->is_allowed_to_view_content_object(),
            $this->canEditLearningPathTreeNode(
                $this->getCurrentLearningPathTreeNode()
            )
        );

        return json_encode($learningPathTreeJSONMapper->getNodes());
    }
}
