<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
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
        $selected_complex_content_object_item = $this->get_application()->get_current_node()->get_complex_content_object_item();
        $lp_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $selected_complex_content_object_item->get_ref());

        $form = $this->get_form($this->get_url(), $lp_item);

        if ($form->validate())
        {
            $succes = $this->set_mastery_score($lp_item, $form->exportValues());
            $message = $succes ? 'MasteryScoreSet' : 'MasteryScoreNotSet';
            $this->redirect(Translation :: get($message), ! $succes, $this->get_application()->get_parameters());
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
     * @param string $url
     * @param \core\repository\content_object\learning_path_item\LearningPathItem $lp_item
     * @return \libraries\format\FormValidator
     */
    public function get_form($url, $lp_item)
    {
        $form = new FormValidator('mastery_score', 'post', $url);

        $values = array();
        for ($i = 0; $i <= 100; $i ++)
            $values[$i] = $i;

        $form->addElement('select', 'mastery_score', Translation :: get('MasteryScore'), $values);

        if ($lp_item->get_mastery_score())
        {
            $form->setDefaults(array('mastery_score' => $lp_item->get_mastery_score()));
        }

        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('SetMasteryScore'));
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        return $form;
    }

    /**
     *
     * @param \core\repository\content_object\learning_path_item\LearningPathItem $lp_item
     * @param string[] $values
     * @return boolean
     */
    public function set_mastery_score($lp_item, $values)
    {
        $lp_item->set_mastery_score($values['mastery_score']);
        return $lp_item->update();
    }
}
