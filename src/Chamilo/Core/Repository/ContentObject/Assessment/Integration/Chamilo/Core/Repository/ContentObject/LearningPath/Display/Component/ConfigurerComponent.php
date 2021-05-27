<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Form\ConfigurationForm;
use Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
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
     *
     * @return string
     */
    public function run()
    {
        $configuration = $this->getCurrentTreeNode()->getTreeNodeData()->getAssessmentConfiguration();

        $form = new ConfigurationForm($configuration, $this->get_url());

        if ($form->validate())
        {
            $succes = $this->configure_feedback($form->exportValues());
            $message = $succes ? 'FeedbackConfigured' : 'FeedbackNotConfigured';

            $this->redirect(Translation::get($message), ! $succes, $this->get_application()->get_parameters());
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @param string[] $values
     * @return boolean
     */
    public function configure_feedback($values)
    {
        $treeNodeData = $this->getCurrentTreeNode()->getTreeNodeData();

        if (isset($values[Configuration::PROPERTY_ALLOW_HINTS]))
        {
            $treeNodeData->setAllowHints((bool) $values[Configuration::PROPERTY_ALLOW_HINTS]);
        }
        else
        {
            $treeNodeData->setAllowHints(false);
        }

        if (isset($values[Configuration::PROPERTY_SHOW_SCORE]))
        {
            $treeNodeData->setShowScore((bool) $values[Configuration::PROPERTY_SHOW_SCORE]);
        }
        else
        {
            $treeNodeData->setShowScore(false);
        }

        if (isset($values[Configuration::PROPERTY_SHOW_CORRECTION]))
        {
            $treeNodeData->setShowCorrection((bool) $values[Configuration::PROPERTY_SHOW_CORRECTION]);
        }
        else
        {
            $treeNodeData->setShowCorrection(false);
        }

        if (isset($values[Configuration::PROPERTY_SHOW_SOLUTION]))
        {
            $treeNodeData->setShowSolution((bool) $values[Configuration::PROPERTY_SHOW_SOLUTION]);
        }
        else
        {
            $treeNodeData->setShowSolution(false);
        }

        if (isset($values[ConfigurationForm::PROPERTY_ANSWER_FEEDBACK_OPTION]))
        {
            $treeNodeData->setShowAnswerFeedback((int) $values[Configuration::PROPERTY_SHOW_ANSWER_FEEDBACK]);
        }
        else
        {
            $treeNodeData->setShowAnswerFeedback(Configuration::ANSWER_FEEDBACK_TYPE_NONE);
        }

        if ($treeNodeData->getShowScore() || $treeNodeData->getShowCorrection() || $treeNodeData->getShowSolution() ||
             $treeNodeData->getShowAnswerFeedback())
        {
            $treeNodeData->setFeedbackLocation((int) $values[Configuration::PROPERTY_FEEDBACK_LOCATION]);
        }
        else
        {
            $treeNodeData->setFeedbackLocation(Configuration::FEEDBACK_LOCATION_TYPE_NONE);
        }

        return $treeNodeData->update();
    }
}
