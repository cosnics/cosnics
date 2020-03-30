<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGeneratorFactory;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeJSONMapper;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

abstract class BaseHtmlTreeComponent extends Manager implements DelegateComponent
{

    private $learning_path_menu;

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

    protected function getBootstrapTreeData()
    {
        $tree = $this->getTree();

        $nodeActionGeneratorFactory = new NodeActionGeneratorFactory(
            Translation::getInstance(), Configuration::getInstance(), ClassnameUtilities::getInstance(),
            $this->get_application()->get_parameters()
        );

        $treeJSONMapper = new TreeJSONMapper(
            $tree, $this->getTreeUser(), $this->showProgressInTree() ? $this->getTrackingService() : null,
            $this->getAutomaticNumberingService(), $nodeActionGeneratorFactory->createNodeActionGenerator(),
            $this->get_application()->get_tree_menu_url(), $this->getCurrentTreeNode(),
            $this->get_application()->is_allowed_to_view_content_object(), $this->canEditTreeNode(
            $this->getCurrentTreeNode()
        )
        );

        return json_encode($treeJSONMapper->getNodes());
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

        $html = array();

        $html[] = '<div class="navbar-learning-path">';

        $html[] = '<div class="navbar-learning-path-actions">';
        $html[] = '<span class="learning-path-action-menu">';

        $previous_node = $currentTreeNode->getPreviousNode();

        if ($previous_node instanceof TreeNode)
        {
            $previous_url = $this->getTreeNodeNavigationUrl($previous_node);
            $label = Translation::get('Previous');
            $glyph = new FontAwesomeGlyph('arrow-left', array('fa-3x'), $label, 'fas');

            $html[] = '<a id="learning-path-navigate-left" href="' . $previous_url . '">' . $glyph->render() . '</a>';
        }
        else
        {
            $label = Translation::get('PreviousNa');
            $glyph = new FontAwesomeGlyph('arrow-left', array('disabled', 'fa-3x'), $label, 'fas');

            $html[] = $glyph->render();
        }

        $isMenuHidden = Session::retrieve('learningPathMenuIsHidden');

        $classes = array('learning-path-action-menu-show', 'fa-3x');
        if ($isMenuHidden != 'true')
        {
            $classes[] = 'hidden';
        }
        $glyph = new FontAwesomeGlyph('list-alt', $classes, Translation::get('ShowMenu'), 'fas');
        $html[] = $glyph->render();

        $classes = array('learning-path-action-menu-hide', 'fa-3x');
        if ($isMenuHidden == 'true')
        {
            $classes[] = 'hidden';
        }
        $glyph = new FontAwesomeGlyph('list-alt', $classes, Translation::get('HideMenu'), 'fas');
        $html[] = $glyph->render();

        $glyph = new FontAwesomeGlyph(
            'expand', array('learning-path-action-fullscreen', 'fa-3x'), Translation::get('DisplayFullScreen'), 'fas'
        );
        $html[] = $glyph->render();

        $next_node = $currentTreeNode->getNextNode();

        if ($next_node instanceof TreeNode)
        {
            $next_url = $this->getTreeNodeNavigationUrl($next_node);
            $label = Translation::get('Next');
            $glyph = new FontAwesomeGlyph('arrow-right', array('fa-3x'), $label, 'fas');

            $html[] = '<a id="learning-path-navigate-right" href="' . $next_url . '">' . $glyph->render() . '</a>';
        }
        else
        {
            $label = Translation::get('NextNa');
            $glyph = new FontAwesomeGlyph('arrow-right', array('disabled', 'fa-3x'), $label, 'fas');

            $html[] = $glyph->render();
        }

        $html[] = '</span>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

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

        $classes = array('col-xs-12', 'col-sm-4', 'col-lg-4', 'learning-path-tree-menu-container');

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

        $javascriptFiles = array(
            'Tree/app.js',
            'Tree/controller/LearningPathHtmlTreeController.js'
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
            Manager::ACTION_REPORTING,
            Manager::ACTION_VIEW_ASSESSMENT_RESULT,
            Manager::ACTION_VIEW_USER_PROGRESS
        );

        $inReportingMode = in_array($this->get_action(), $reportingActions);

        $translationVariables = array(
            'AddNewPage',
            'AddNewSection',
            'StartStructureQuickEditMode',
            'StopStructureQuickEditMode',
            'EditTitle',
            'AddFrom'
        );

        $commonTranslationVariables = array('Remove', 'Confirm', 'Create', 'Import');

        $translator = Translation::getInstance();
        $translations = array();

        foreach ($translationVariables as $translationVariable)
        {
            $translations[$translationVariable] = $translator->getTranslation($translationVariable);
        }

        foreach ($commonTranslationVariables as $commonTranslationVariable)
        {
            $translations[$commonTranslationVariable] =
                $translator->getTranslation($commonTranslationVariable, null, Utilities::COMMON_LIBRARIES);
        }

        $parameters = array(
            'fetchTreeNodesAjaxUrl' => $this->get_application()->get_url(
                array(self::PARAM_ACTION => self::ACTION_AJAX, self::PARAM_REPORTING_MODE => (int) $inReportingMode)
            ),
            'moveTreeNodeAjaxUrl' => $this->get_application()->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_MOVE_LEARNING_PATH_TREE_NODE
                )
            ),
            'addTreeNodeAjaxUrl' => $this->get_application()->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_ADD_LEARNING_PATH_TREE_NODE
                )
            ),
            'updateTreeNodeTitleAjaxUrl' => $this->get_application()->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_UPDATE_LEARNING_PATH_TREE_NODE_TITLE
                )
            ),
            'deleteTreeNodeAjaxUrl' => $this->get_application()->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager::ACTION_DELETE_LEARNING_PATH_TREE_NODE
                )
            ),
            'canEditTree' => $this->canEditCurrentTreeNode() ? 'true' : 'false',
            'inReportingMode' => $inReportingMode ? 'true' : 'false',
            'treeData' => $this->getBootstrapTreeData(),
            'translationsJSON' => json_encode($translations)
        );

        foreach ($parameters as $parameter => $value)
        {
            $learningPathHtmlTree = str_replace('{{ ' . $parameter . ' }}', $value, $learningPathHtmlTree);
        }

        //        $html[] = $this->learning_path_menu->render();
        $html[] = $learningPathHtmlTree;

        $html[] = '</div>';

        if ($this->get_action() == self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT)
        {
            $trackingService = $this->getTrackingService();
            $progress = $trackingService->getLearningPathProgress(
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

        $classes = array('col-xs-12', 'col-sm-8', 'col-lg-8', 'learning-path-content');

        if ($isMenuHidden == 'true')
        {
            $classes[] = 'learning-path-content-full-screen';
        }

        $html[] = '<div class="' . implode(' ', $classes) . '">';

        if ($this->inStudentView())
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
     * Returns whether or not the progress should be shown
     *
     * @return bool
     */
    protected function showProgressInTree()
    {
        return true;
    }
}
