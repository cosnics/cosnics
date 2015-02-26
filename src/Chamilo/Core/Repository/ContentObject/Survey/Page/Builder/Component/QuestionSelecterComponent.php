<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\Component\ViewerComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: question_selecter.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.survey.component
 */
class QuestionSelecterComponent extends Manager
{

    function run()
    {
        $survey_id = Request :: get(\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);
        if ($survey_id)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_PARENT),
                new StaticConditionVariable($survey_id));

            $clois = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
                ComplexContentObjectItem :: class_name(),
                new DataClassRetrievesParameters($condition));

            while ($cloi = $clois->next_result())
            {
                $question_ids[] = $cloi->get_ref();
            }
        }
        else
        {
            $question_ids = Request :: get(self :: PARAM_QUESTION_ID);
            if (! is_array($question_ids))
                $question_ids = array($question_ids);
        }

        if (count($question_ids) == 0)
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager :: ACTION_BROWSE)),
                    $this->get_root_content_object()->get_title()));
            $trail->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => self :: ACTION_MERGE_SURVEY_PAGE,
                            ViewerComponent :: PARAM_ACTION => ViewerComponent :: ACTION_PUBLISHER,
                            ViewerComponent :: PARAM_ID => Request :: get(ViewerComponent :: PARAM_ID))),
                    Translation :: get('MergePage')));

            return $this->display_error_page(Translation :: get('NoQuestionsSelected'));
        }

        $succes = true;

        $parent = $this->get_root_content_object()->get_id();

        foreach ($question_ids as $question_id)
        {
            $question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $question_id);

            $cloi = ComplexContentObjectItem :: factory($question->get_type());
            $cloi->set_parent($parent);
            $cloi->set_ref($question_id);
            $cloi->set_user_id($this->get_user_id());
            $cloi->set_display_order(\Chamilo\Core\Repository\Storage\DataManager :: select_next_display_order($parent));
            $succes &= $cloi->create();
        }

        $message = $succes ? Translation :: get('QuestionsAdded') : Translation :: get('QuestionsNotAdded');

        $this->redirect($message, ! $succes, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }
}
?>