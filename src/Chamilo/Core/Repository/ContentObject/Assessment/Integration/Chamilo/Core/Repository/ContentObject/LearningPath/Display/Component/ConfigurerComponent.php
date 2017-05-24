<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Form\ConfigurationForm;
use Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Translation;

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
     */
    public function run()
    {
        $configuration = $this->getCurrentLearningPathTreeNode()->getLearningPathChild()->getAssessmentConfiguration();

        $form = new ConfigurationForm($configuration, $this->get_url());
        
        if ($form->validate())
        {
            $succes = $this->configure_feedback($form->exportValues());
            $message = $succes ? 'FeedbackConfigured' : 'FeedbackNotConfigured';
            
            $this->redirect(Translation::get($message), ! $succes, $this->get_application()->get_parameters());
        }
        else
        {
            $html = array();
            
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
        $learningPathChild = $this->getCurrentLearningPathTreeNode()->getLearningPathChild();
        
        if (isset($values[Configuration::PROPERTY_ALLOW_HINTS]))
        {
            $learningPathChild->setAllowHints((bool) $values[Configuration::PROPERTY_ALLOW_HINTS]);
        }
        else
        {
            $learningPathChild->setAllowHints(false);
        }
        
        if (isset($values[Configuration::PROPERTY_SHOW_SCORE]))
        {
            $learningPathChild->setShowScore((bool) $values[Configuration::PROPERTY_SHOW_SCORE]);
        }
        else
        {
            $learningPathChild->setShowScore(false);
        }
        
        if (isset($values[Configuration::PROPERTY_SHOW_CORRECTION]))
        {
            $learningPathChild->setShowCorrection((bool) $values[Configuration::PROPERTY_SHOW_CORRECTION]);
        }
        else
        {
            $learningPathChild->setShowCorrection(false);
        }
        
        if (isset($values[Configuration::PROPERTY_SHOW_SOLUTION]))
        {
            $learningPathChild->setShowSolution((bool) $values[Configuration::PROPERTY_SHOW_SOLUTION]);
        }
        else
        {
            $learningPathChild->setShowSolution(false);
        }
        
        if (isset($values[ConfigurationForm::PROPERTY_ANSWER_FEEDBACK_OPTION]))
        {
            $learningPathChild->setShowAnswerFeedback((int) $values[Configuration::PROPERTY_SHOW_ANSWER_FEEDBACK]);
        }
        else
        {
            $learningPathChild->setShowAnswerFeedback(Configuration::ANSWER_FEEDBACK_TYPE_NONE);
        }
        
        if ($learningPathChild->getShowScore() || $learningPathChild->getShowCorrection() ||
             $learningPathChild->getShowSolution() || $learningPathChild->getShowAnswerFeedback())
        {
            $learningPathChild->setFeedbackLocation((int) $values[Configuration::PROPERTY_FEEDBACK_LOCATION]);
        }
        else
        {
            $learningPathChild->setFeedbackLocation(Configuration::FEEDBACK_LOCATION_TYPE_NONE);
        }
        
        return $learningPathChild->update();
    }
}
