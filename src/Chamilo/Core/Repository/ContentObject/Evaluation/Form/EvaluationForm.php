<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Form;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.evaluation
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * A form to create/update a evaluation
 */
class EvaluationForm extends ContentObjectForm
{

    // Inherited
    public function create_content_object()
    {
        $object = new Evaluation();
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    /**
     * @param array $htmleditor_options
     * @param bool $in_tab
     */
    public function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);
        //$this->buildEvaluationForm();
    }

    /**
     * @param array $htmleditor_options
     * @param bool $in_tab
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Doctrine\ORM\ORMException
     */
    public function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($htmleditor_options, $in_tab);
        //$object = $this->get_content_object();
        //$this->buildEvaluationForm($object->useScores(), $object->useFeedback());
    }

    /**
     * Builds the form for the additional evaluation properties
     * @param bool $use_scores
     */
    /*protected function buildEvaluationForm($use_scores = false, $use_feedback = false)
    {
        $translator = Translation::getInstance();
        $this->addElement('category', $translator->getTranslation('Properties'));
        $el = $this->addElement(
            'checkbox', Evaluation::PROPERTY_USE_SCORES,
            $translator->getTranslation('EvaluationUseScores')
        );
        $el->setChecked($use_scores);
        $el = $this->addElement(
            'checkbox', Evaluation::PROPERTY_USE_FEEDBACK,
            $translator->getTranslation('EvaluationUseFeedback')
        );
        $el->setChecked($use_feedback);
    }*/

    /*public function update_content_object()
    {
        $object = $this->get_content_object();

        $values = $this->exportValues();
        $useScores = (bool) $values[Evaluation::PROPERTY_USE_SCORES];
        $useFeedback = (bool) $values[Evaluation::PROPERTY_USE_FEEDBACK];

        $object->setUseScores($useScores);
        $object->setUseFeedback($useFeedback);

        return parent::update_content_object();
    }*/
}