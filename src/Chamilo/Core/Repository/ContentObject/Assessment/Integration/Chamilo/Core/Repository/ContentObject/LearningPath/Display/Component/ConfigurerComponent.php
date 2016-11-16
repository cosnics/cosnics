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
     *
     * @var \core\repository\content_object\learning_path_item\LearningPathItem
     */
    private $learning_path_item;

    public function run()
    {
        $selected_complex_content_object_item = $this->get_application()->get_current_node()->get_complex_content_object_item();
        $this->learning_path_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $selected_complex_content_object_item->get_ref());
        
        $form = new ConfigurationForm($this->learning_path_item->get_configuration(), $this->get_url());
        
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
        if (isset($values[Configuration::PROPERTY_ALLOW_HINTS]))
        {
            $this->learning_path_item->set_allow_hints($values[Configuration::PROPERTY_ALLOW_HINTS]);
        }
        else
        {
            $this->learning_path_item->set_allow_hints(0);
        }
        
        if (isset($values[Configuration::PROPERTY_SHOW_SCORE]))
        {
            $this->learning_path_item->set_show_score($values[Configuration::PROPERTY_SHOW_SCORE]);
        }
        else
        {
            $this->learning_path_item->set_show_score(0);
        }
        
        if (isset($values[Configuration::PROPERTY_SHOW_CORRECTION]))
        {
            $this->learning_path_item->set_show_correction($values[Configuration::PROPERTY_SHOW_CORRECTION]);
        }
        else
        {
            $this->learning_path_item->set_show_correction(0);
        }
        
        if (isset($values[Configuration::PROPERTY_SHOW_SOLUTION]))
        {
            $this->learning_path_item->set_show_solution($values[Configuration::PROPERTY_SHOW_SOLUTION]);
        }
        else
        {
            $this->learning_path_item->set_show_solution(0);
        }
        
        if (isset($values[ConfigurationForm::PROPERTY_ANSWER_FEEDBACK_OPTION]))
        {
            $this->learning_path_item->set_show_answer_feedback($values[Configuration::PROPERTY_SHOW_ANSWER_FEEDBACK]);
        }
        else
        {
            $this->learning_path_item->set_show_answer_feedback(Configuration::ANSWER_FEEDBACK_TYPE_NONE);
        }
        
        if ($this->learning_path_item->get_show_score() || $this->learning_path_item->get_show_correction() ||
             $this->learning_path_item->get_show_solution() || $this->learning_path_item->get_show_answer_feedback())
        {
            $this->learning_path_item->set_feedback_location($values[Configuration::PROPERTY_FEEDBACK_LOCATION]);
        }
        else
        {
            $this->learning_path_item->set_feedback_location(Configuration::FEEDBACK_LOCATION_TYPE_NONE);
        }
        
        return $this->learning_path_item->update();
    }
}
