<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: question_selecter.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.assessment.component
 */
class QuestionSelecterComponent extends Manager
{

    public function run()
    {
        $assessment_id = Request :: get(self :: PARAM_ASSESSMENT_ID);

        if ($assessment_id)
        {
            $clois = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
                ComplexContentObjectItem :: class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem :: class_name(),
                        ComplexContentObjectItem :: PROPERTY_PARENT),
                    new StaticConditionVariable($assessment_id),
                    ComplexContentObjectItem :: get_table_name()));
            while ($cloi = $clois->next_result())
            {
                $question_ids[] = $cloi->get_ref();
            }
        }
        else
        {
            $question_ids = $this->getRequest()->get(self :: PARAM_QUESTION_ID);

            if (! is_array($question_ids))
                $question_ids = array($question_ids);
        }

        if (count($question_ids) == 0)
        {
            $trail = BreadcrumbTrail :: getInstance();
            $trail->add(
                new Breadcrumb(
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)),
                    $this->get_root_content_object()->get_title()));
            $trail->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_MERGE_ASSESSMENT,
                            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID => Request :: get(
                                \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID))),
                    Translation :: get('MergeAssessment')));

            return $this->display_error_page(Translation :: get('NoQuestionsSelected'));
        }

        $succes = true;

        $parent = $this->get_root_content_object()->get_id();

        foreach ($question_ids as $question_id)
        {
            $question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $question_id);

            $contentObjectClassName = $question->package() . '\Storage\DataClass\\' .
                 ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($question->package());

            $cloi = ComplexContentObjectItem :: factory($contentObjectClassName);

            $cloi->set_ref($question_id);
            $cloi->set_parent($parent);
            $cloi->set_user_id($this->get_user_id());
            $cloi->set_display_order(\Chamilo\Core\Repository\Storage\DataManager :: select_next_display_order($parent));

            $succes &= $cloi->create();
        }

        $message = $succes ? Translation :: get('QuestionsAdded') : Translation :: get('QuestionsNotAdded');

        $this->redirect(
            $message,
            ! $succes,
            array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE,
                \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID => Request :: get(
                    \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID)));
    }
}
