<?php

namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\PathBuilder;
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
        $treeNodeData = $this->getCurrentTreeNode()->getTreeNodeData();

        $form = $this->get_form($this->get_url(), $treeNodeData);

        if ($form->validate())
        {
            $succes = $this->set_mastery_score($treeNodeData, $form->exportValues());
            $message = $succes ? 'MasteryScoreSet' : 'MasteryScoreNotSet';
            $this->redirect(Translation::get($message), !$succes, $this->get_application()->get_parameters());
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        return null;
    }

    /**
     * Renders a template from a given context
     *
     * @param string $context
     * @param string $template
     *
     * @return string
     */
    protected function renderTemplate($context, $template, $parameters = array())
    {
        $templatePath = $this->getPathBuilder()->getTemplatesPath($context) . $template;

        if (!file_exists($templatePath))
        {
            throw new \InvalidArgumentException(
                sprintf('The given template %s in context %s could not be found', $template, $context)
            );
        }

        $contents = file_get_contents($templatePath);

        if ($contents === false)
        {
            throw new \RuntimeException(
                sprintf('The given template %s in context %s could not be loaded', $template, $context)
            );
        }

        foreach($parameters as $variable => $value)
        {
            $contents = str_replace('{ ' . $variable . ' }', $value, $contents);
            $contents = str_replace('{' . $variable . '}', $value, $contents);
        }

        return $contents;
    }

    public function get_form($url, TreeNodeData $treeNodeData)
    {
        $form = new FormValidator('mastery_score', 'post', $url);

        $values = array();
        for ($i = 0; $i <= 100; $i ++)
        {
            $value = $i == 0 ? Translation::getInstance()->getTranslation('NoMasteryScore') : $i . '%';
            $values[$i] = $value;
        }

        $form->addElement('select', 'mastery_score', Translation::get('MasteryScore'), $values);

        if ($treeNodeData->getMasteryScore())
        {
            $form->setDefaults(array('mastery_score' => $treeNodeData->getMasteryScore()));
        }

        $form->addElement('html', $this->renderTemplate(Manager::context(), 'MasteryScoreSlider.html'));

        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation::get('SetMasteryScore'));
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        return $form;
    }

    public function set_mastery_score(TreeNodeData $treeNodeData, $values)
    {
        $treeNodeData->setMasteryScore((int) $values['mastery_score']);

        return $treeNodeData->update();
    }
}
