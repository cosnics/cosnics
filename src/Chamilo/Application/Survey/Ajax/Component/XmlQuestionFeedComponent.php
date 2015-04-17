<?php
namespace Chamilo\Application\Survey\Ajax\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;

/**
 *
 * @package Chamilo\Application\Survey\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class XmlQuestionFeedComponent extends \Chamilo\Application\Survey\Ajax\Manager
{

    public function run()
    {
        $conditions = array();

        $publication_id = $_GET[Manager :: PARAM_PUBLICATION_ID];
        $context_template_id = $_GET[Manager :: PARAM_CONTEXT_TEMPLATE_ID];

        $pub = DataManager :: retrieve_by_id(Publication :: class_name(), $publication_id);
        $survey = $pub->get_publication_object();

        if ($context_template_id)
        {
            $complex_questions = $survey->get_complex_questions_for_context_template_ids(array($context_template_id));
        }
        else
        {
            $complex_questions = $survey->get_complex_questions();
        }

        $c = array();
        if (is_array($_GET['exclude']))
        {
            foreach ($_GET['exclude'] as $id)
            {
                $c[] = $id;
            }
        }

        if (count($conditions) > 0)
        {
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = null;
        }

        foreach ($complex_questions as $complex_question_id => $complex_question)
        {

            $question = $complex_question->get_ref_object();
            $question_id = $question->get_id();
            if (! $question instanceof Description)
            {
                if (! in_array($complex_question_id, $c))
                {
                    $questions[$complex_question_id] = $question->get_title();
                }
            }
        }

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

        $this->dump_tree($questions);

        echo '</tree>';
    }

    function dump_tree($questions)
    {
        if ($this->contains_results($questions))
        {
            echo '<node id="0" classes="category unlinked" title="' . Translation :: get('Questions') . '">' . "\n";

            foreach ($questions as $complex_question_id => $question_title)
            {
                $id = 'question_' . $complex_question_id;
                echo '<leaf id="' . $id . '" classes="" title="' . htmlspecialchars($question_title) . '" description="' .
                     htmlspecialchars($question_title) . '"/>' . "\n";
            }

            echo '</node>' . "\n";
        }
    }

    function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }
        return false;
    }
}