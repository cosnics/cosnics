<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Form;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ScoreForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    private $score;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    private $assignmentDataProvider;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param string $postUrl
     * @param \Twig_Environment $twig
     */
    public function __construct(
        Score $score = null, AssignmentDataProvider $assignmentDataProvider, $postUrl, \Twig_Environment $twig
    )
    {
        parent::__construct('details', 'post', $postUrl);

        $this->score = $score;
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->twig = $twig;

        $this->buildForm();
        $this->setDefaults();
    }

    protected function buildForm()
    {
        $this->addElement('select', Score::PROPERTY_SCORE, Translation::get('Score'), $this->getScoreChoices());

        $this->addElement(
            'style_button',
            null,
            '',//Translation::get('Save', null, Utilities::COMMON_LIBRARIES),
            ['id' => 'scoreSaveButton'],
            null,
            'floppy-save'
        );

        $this->addElement('html', $this->twig->render(Manager::context() . ':ScoreSlider.html.twig'));
    }

    protected function getScoreChoices()
    {
        $choices = array();

        $choices[- 1] = Translation::get('NoScore');

        for ($i = 0; $i <= 100; $i ++)
        {
            $choices[$i] = $i . '%';
        }

        return $choices;
    }

    public function setDefaults()
    {
        $defaultValues = array();

        if ($this->score instanceof Score)
        {
            $defaultValues[Score::PROPERTY_SCORE] = $this->score->getScore();
        }

        return parent::setDefaults($defaultValues);
    }
}