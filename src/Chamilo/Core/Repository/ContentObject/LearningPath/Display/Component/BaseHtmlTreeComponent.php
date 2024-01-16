<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGeneratorFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
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
        $learning_path = $this->learningPath;

        $trail = BreadcrumbTrail::getInstance();

        if (!$learning_path)
        {
            return $this->display_error_page(Translation::get('NoObjectSelected'));
        }

//        $this->learning_path_menu = new TreeRenderer(
//            $this->getTree(), $this,
//            $this->getTrackingService(),
//            $this->getAutomaticNumberingService(),
//            $this->get_parent()->get_tree_menu_url(), 'learning-path-menu'
//        );

        $parentAndCurrentNodes = $this->getCurrentTreeNode()->getParentNodes();
        $parentAndCurrentNodes[] = $this->getCurrentTreeNode();

        $automaticNumberingService = $this->getAutomaticNumberingService();

        foreach ($parentAndCurrentNodes as $parentNode)
        {
            $title = $automaticNumberingService->getAutomaticNumberedTitleForTreeNode($parentNode);
            $url = $this->getTreeNodeNavigationUrl($parentNode);

            $trail->add(new Breadcrumb($url, $title));
        }

        return $this->build();
    }

    abstract function build();

    /**
     */
    public function render_header($pageTitle = '')
    {
        $isMenuHidden = Session::retrieve('learningPathMenuIsHidden');

        $html = array();

        $html[] = parent::render_header($pageTitle);

        $html[] = '<div class="learning-path-display">';

        $html[] = $this->get_navigation_bar();

        $html[] = '<div class="row">';

        // Menu

        $classes = array('col', 'col-sm-4', 'col-lg-4', 'learning-path-tree-menu-container');

        if ($isMenuHidden == 'true')
        {
            $classes[] = 'd-none';
        }
        else
        {
            $classes[] = 'd-block';
        }

        $html[] = '<div class="' . implode(' ', $classes) . '">';

        $html[] = '<h3>';
        $html[] = Translation::get('LearningPathNavigationMenu');
        $html[] = '</h3>';

        $javascriptFiles = array(
            'Controller/LearningPathHtmlTreeController.js'
        );

        foreach ($javascriptFiles as $javascriptFile)
        {
            $html[] = ResourceManager::getInstance()->get_resource_html(
                $this->getPathBuilder()->getResourcesPath(Manager::context(), true) . 'Javascript/' . $javascriptFile
            );
        }

        $learningPathHtmlTreePath = Path::getInstance()->getResourcesPath(
                "Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Display"
            ) . '/Templates/LearningPathHtmlTree.html';
        $learningPathHtmlTree = file_get_contents($learningPathHtmlTreePath);

        $reportingActions = array(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_REPORTING,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_ASSESSMENT_RESULT,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS
        );

        $inReportingMode = in_array($this->get_action(), $reportingActions);

        $translationVariables = array(
            'AddNewPage', 'AddNewSection', 'StartStructureQuickEditMode', 'StopStructureQuickEditMode', 'EditTitle',
            'AddFrom'
        );

        $commonTranslationVariables = array('Remove', 'Confirm', 'Create', 'Import');

        $translator = Translation::getInstance();
        $translations = array();

        foreach($translationVariables as $translationVariable)
        {
            $translations[$translationVariable] = $translator->getTranslation($translationVariable);
        }

        foreach($commonTranslationVariables as $commonTranslationVariable)
        {
            $translations[$commonTranslationVariable] = $translator->getTranslation($commonTranslationVariable, null, Utilities::COMMON_LIBRARIES);
        }

        $parameters = array(
            'apiConfig' => array(
                'fetchTreeNodesAjaxUrl' => $this->get_application()->get_url(array(self::PARAM_ACTION => self::ACTION_AJAX, self::PARAM_REPORTING_MODE => (int) $inReportingMode)),
                'moveTreeNodeAjaxUrl' => $this->get_application()->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION
                        => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_MOVE_LEARNING_PATH_TREE_NODE
                    )
                ),
                'addTreeNodeAjaxUrl' => $this->get_application()->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION
                        => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_ADD_LEARNING_PATH_TREE_NODE
                    )
                ),
                'updateTreeNodeTitleAjaxUrl' => $this->get_application()->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION
                        => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_UPDATE_LEARNING_PATH_TREE_NODE_TITLE
                    )
                ),
                'deleteTreeNodeAjaxUrl' => $this->get_application()->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_AJAX,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION
                        => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_DELETE_LEARNING_PATH_TREE_NODE
                    )
                )
            ),
            'canEditTree' => $this->canEditCurrentTreeNode(),
            'canViewReporting' => $this->canViewReporting(),
            'inReportingMode' => $inReportingMode,
            'treeData' => $this->getBootstrapTreeData(),
            'translations' => $translations
        );

        $html[] = '<script id="learning-path-tree-menu-app-data" type="application/json">';
        $html[] = json_encode($parameters);
        $html[] = '</script>';

        $html[] = '<div class="learning-path-tree-menu">';

//        $html[] = $this->learning_path_menu->render();
        $html[] = $learningPathHtmlTree;

        $html[] = '</div>';

        if ($this->get_action() == self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT)
        {
            $trackingService = $this->getTrackingService();
            $progress =
                $trackingService->getLearningPathProgress(
                    $this->learningPath, $this->getUser(), $this->getTree()->getRoot()
                );

            $progressBarRenderer = new ProgressBarRenderer();

            $html[] = '<a href="' . $this->get_url(
                    array(self::PARAM_ACTION => self::ACTION_REPORTING), array(self::PARAM_CHILD_ID)
                ) . '">';

            $html[] = $progressBarRenderer->render($progress, ProgressBarRenderer::MODE_DEFAULT, 0, true);
            $html[] = '</a>';
        }

        $html[] = '</div>';

        // Content

        $classes = ['col'];
        if ($isMenuHidden != 'true')
        {
            $classes[] = 'col-sm-8';
            $classes[] = 'col-lg-8';
        }
        $classes[] = 'learning-path-content';

        $html[] = '<div class="' . implode(' ', $classes) . '">';

        if($this->inStudentView())
        {
            $disableStudentViewUrl = $this->get_url(array(self::PARAM_ACTION => self::ACTION_DISABLE_STUDENT_VIEW));

            $html[] = '<div class="alert alert-info">';
            $html[] = '<div class="pull-left" style="margin-top: 6px;">';
            $html[] = $translator->getTranslation('CurrentlyInStudentView');
            $html[] = '</div>';
            $html[] = '<a class="btn btn-default btn-sm pull-right" href="' . $disableStudentViewUrl . '">';
            $html[] = $translator->getTranslation('DisableStudentView');
            $html[] = '</a>';
            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';
        }

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

        /*$html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::package(), true) . 'LearningPathMenu.js'
        );
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Utilities::COMMON_LIBRARIES, true) .
            'Plugin/Jquery/jquery.fullscreen.min.js'
        );*/

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
        $currentTreeNode = $this->getCurrentTreeNode();
        $previous_node = $currentTreeNode->getPreviousNode();
        $has_previous_node = $previous_node instanceof TreeNode;
        $previous_url = $has_previous_node ? $this->getTreeNodeNavigationUrl($previous_node) : null;
        $next_node = $currentTreeNode->getNextNode();
        $has_next_node = $next_node instanceof TreeNode;
        $next_url = $has_next_node ?$this->getTreeNodeNavigationUrl($next_node) : null;

        return $this->getTwig()->render(\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::context() . ':NavigationBar.html.twig', [
            'HAS_PREVIOUS_NODE' => $has_previous_node ? 'true' : 'false',
            'PREVIOUS_URL' => $previous_url,
            'IS_MENU_HIDDEN' => Session::retrieve('learningPathMenuIsHidden') == 'true' ? 'true' : 'false',
            'HAS_NEXT_NODE' => $has_next_node ? 'true' : 'false',
            'NEXT_URL' => $next_url
        ]);

        /*$html = array();

        $html[] = '<div class="navbar-learning-path">';

        $html[] = '<div class="navbar-learning-path-actions">';
        $html[] = '<span class="learning-path-action-menu">';

        $previous_node = $currentTreeNode->getPreviousNode();

        if ($previous_node instanceof TreeNode)
        {
            $previous_url = $this->getTreeNodeNavigationUrl($previous_node);
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

        $next_node = $currentTreeNode->getNextNode();

        if ($next_node instanceof TreeNode)
        {
            $next_url = $this->getTreeNodeNavigationUrl($next_node);
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
        $html[] = '</div>';

        return implode(PHP_EOL, $html);*/
    }

    protected function getBootstrapTreeData()
    {
        $tree = $this->getTree();

        $nodeActionGeneratorFactory =
            new NodeActionGeneratorFactory(Translation::getInstance(), Configuration::getInstance(), ClassnameUtilities::getInstance(), $this->get_application()->get_parameters());

        $treeJSONMapper = new TreeJSONMapper(
            $tree, $this->getTreeUser(),
            $this->showProgressInTree() ? $this->getTrackingService() : null,
            $this->getAutomaticNumberingService(),
            $nodeActionGeneratorFactory->createNodeActionGenerator(),
            $this->get_application()->get_tree_menu_url(),
            $this->getCurrentTreeNode(),
            $this->get_application()->is_allowed_to_view_content_object(),
            $this->canEditTreeNode(
                $this->getCurrentTreeNode()
            ),
            $this->canViewReporting()
        );

        return $treeJSONMapper->getNodes();
    }

    /**
     * Returns the user that is used to calculate and render the progress in the tree
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    protected function getTreeUser()
    {
        return $this->getUser();
    }

    /**
     * Returns whether or not the progress should be shown
     *
     * @return bool
     */
    protected function showProgressInTree()
    {
        return true;
    }
}
