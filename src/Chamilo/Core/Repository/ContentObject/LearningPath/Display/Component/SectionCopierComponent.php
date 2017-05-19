<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGeneratorFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component to copy sections from other learning paths to the current learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SectionCopierComponent extends BaseHtmlTreeComponent
{

    function build()
    {
        if (!$this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            throw new NotAllowedException();
        }

        $breadcrumbTrail = BreadcrumbTrail::getInstance();
        $breadcrumbTrail->add(
            new Breadcrumb($this->get_url(), Translation::getInstance()->getTranslation('SectionCopierComponent'))
        );
//
//        if (!\Chamilo\Core\Repository\Viewer\Manager::any_object_selected())
//        {
//            $this->getRequest()->request->set(
//                \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION,
//                \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER
//            );
//
//            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
//
//            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);
//            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);
//            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_BREADCRUMBS_DISABLED, true);
//
//            $factory = new ApplicationFactory(
//                \Chamilo\Core\Repository\Viewer\Manager::context(),
//                $applicationConfiguration
//            );
//
//            return $factory->run();
//        }
//        else
//        {
//            $html = array();
//
//            $html[] = $this->render_header();
//            $html[] = $this->render_footer();
//
//            return implode(PHP_EOL, $html);
//        }

        $html = array();

        $html[] = $this->render_header();

        $sectionCopierHtml = file_get_contents(
            $this->getPathBuilder()->getResourcesPath(Manager::context()) . 'Templates/SectionCopier.html'
        );

        $parameters = array(
            'treeData' => $this->getBootstrapTreeData()
        );

        foreach ($parameters as $parameter => $value)
        {
            $sectionCopierHtml = str_replace('{{ ' . $parameter . ' }}', $value, $sectionCopierHtml);
        }

        $html[] = $sectionCopierHtml;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function getBootstrapTreeData()
    {
        $learningPathTree = $this->getLearningPathTree();

        $nodeActionGeneratorFactory =
            new NodeActionGeneratorFactory(
                Translation::getInstance(), Configuration::getInstance(), ClassnameUtilities::getInstance(),
                $this->get_application()->get_parameters()
            );

        $learningPathTreeJSONMapper = new LearningPathTreeJSONMapper(
            $learningPathTree, $this->getUser(),
            $this->getLearningPathTrackingService(),
            $this->getAutomaticNumberingService(),
            $nodeActionGeneratorFactory->createNodeActionGenerator(),
            $this->get_application()->get_learning_path_tree_menu_url(),
            $this->getCurrentLearningPathTreeNode(),
            $this->get_application()->is_allowed_to_view_content_object(),
            $this->canEditLearningPathTreeNode(
                $this->getCurrentLearningPathTreeNode()
            )
        );

        return json_encode($learningPathTreeJSONMapper->getNodes());
    }

//    /**
//     * Overwrite render header to add the wizard
//     *
//     * @return string
//     */
//    public function render_header()
//    {
//        $translator = Translation::getInstance();
//
//        $html = array();
//        $html[] = parent::render_header();
//
//        $wizardHeader = new WizardHeader();
//        $wizardHeader->setStepTitles(
//            array(
//                $translator->getTranslation('SelectLearningObjectStep'),
//                $translator->getTranslation('SelectSectionStep')
//            )
//        );
//
//        $selectedStepIndex = \Chamilo\Core\Repository\Viewer\Manager::any_object_selected() ? 1 : 0;
//        $wizardHeader->setSelectedStepIndex($selectedStepIndex);
//
//        $wizardHeaderRenderer = new WizardHeaderRenderer($wizardHeader);
//
//        $html[] = $wizardHeaderRenderer->render();
//
//        return implode(PHP_EOL, $html);
//    }
//
//    /**
//     * @return array
//     */
//    public function get_allowed_content_object_types()
//    {
//        return array(LearningPath::class_name());
//    }
}