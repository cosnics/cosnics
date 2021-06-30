<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form\ConfigurationFormBuilder;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\EvaluationConfiguration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package core\repository\content_object\assessment\integration\core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigurerComponent extends Manager implements DelegateComponent
{

    /**
     * @return string
     * @throws \Exception
     *
     * @throws \HTML_QuickForm_Error
     * @throws \PEAR_Error
     */
    public function run()
    {
        /** @var EvaluationConfiguration $configuration */
        $configuration = $this->getCurrentTreeNode()->getConfiguration(new EvaluationConfiguration());

        $form = new FormValidator('configurer', 'post', $this->get_url());

        $formBuilder = new ConfigurationFormBuilder($this->getTranslator());
        $formBuilder->buildForm($form, $configuration);

        if ($form->validate())
        {
            $success = $this->configure($form->exportValues(), $this->getCurrentTreeNode());
            $message = $success ? 'EvaluationConfigured' : 'EvaluationNotConfigured';

            $this->redirect(Translation::get($message), ! $success, $this->get_application()->get_parameters(), [self::PARAM_ACTION, \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION]);

            return null;
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param $exportValues
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return bool
     */
    protected function configure($exportValues, TreeNode $treeNode)
    {
        /** @var EvaluationConfiguration $configuration */
        $configuration = $treeNode->getConfiguration(new EvaluationConfiguration());
        $configuration->setEntityType($exportValues[ConfigurationFormBuilder::FORM_PROPERTY_ENTITY_TYPE]);
        $configuration->setOpenForStudents($exportValues[ConfigurationFormBuilder::FORM_PROPERTY_OPEN_FOR_STUDENTS] == 1);

        $treeNode->setConfiguration($configuration);

        try
        {
            $this->getTreeNodeDataService()->storeConfigurationForTreeNode($treeNode);
        }
        catch(\Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     * @return TreeNodeDataService
     */
    protected function getTreeNodeDataService()
    {
        return $this->getService(TreeNodeDataService::class);
    }
}
