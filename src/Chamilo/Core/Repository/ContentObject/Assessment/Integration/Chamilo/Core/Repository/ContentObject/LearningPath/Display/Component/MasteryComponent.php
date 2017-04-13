<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\assessment\integration\core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MasteryComponent extends Manager
{

    public function run()
    {
        $learningPathChild = $this->getCurrentLearningPathTreeNode()->getLearningPathChild();

        $form = $this->get_form($this->get_url(), $learningPathChild);
        
        if ($form->validate())
        {
            $succes = $this->set_mastery_score($learningPathChild, $form->exportValues());
            $message = $succes ? 'MasteryScoreSet' : 'MasteryScoreNotSet';
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

    public function get_form($url, LearningPathChild $learningPathChild)
    {
        $form = new FormValidator('mastery_score', 'post', $url);
        
        $values = array();
        for ($i = 0; $i <= 100; $i ++)
        {
            $value = $i == 0 ? Translation::getInstance()->getTranslation('NoMasteryScore') : $i . '%';
            $values[$i] = $value;
        }
        
        $form->addElement('select', 'mastery_score', Translation::get('MasteryScore'), $values);
        
        if ($learningPathChild->getMasteryScore())
        {
            $form->setDefaults(array('mastery_score' => $learningPathChild->getMasteryScore()));
        }
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation::get('SetMasteryScore'));
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        return $form;
    }

    public function set_mastery_score(LearningPathChild $learningPathChild, $values)
    {
        $learningPathChild->setMasteryScore((int) $values['mastery_score']);
        return $learningPathChild->update();
    }
}
