<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Form;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Twig\Environment;

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
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    private $assignmentDataProvider;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    private $score;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param string $postUrl
     * @param \Twig\Environment $twig
     */
    public function __construct(
        Score $score = null, AssignmentDataProvider $assignmentDataProvider, $postUrl, Environment $twig
    )
    {
        parent::__construct('details', self::FORM_METHOD_POST, $postUrl);

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
            'style_button', null, '',//Translation::get('Save', null, StringUtilities::LIBRARIES),
            ['id' => 'scoreSaveButton'], null, new FontAwesomeGlyph('floppy-save')
        );

        $this->addElement('html', $this->twig->render(Manager::CONTEXT . ':ScoreSlider.html.twig'));
    }

    protected function getScoreChoices()
    {
        $choices = [];

        $choices[- 1] = Translation::get('NoScore');

        for ($i = 0; $i <= 100; $i ++)
        {
            $choices[$i] = $i . '%';
        }

        return $choices;
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $defaultValues = [];

        if ($this->score instanceof Score)
        {
            $defaultValues[Score::PROPERTY_SCORE] = $this->score->getScore();
        }

        return parent::setDefaults($defaultValues, $filter);
    }
}
