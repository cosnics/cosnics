<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * Shows the information block for a single user with a single question of a single assessment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentQuestionUserInformationBlock extends AssessmentQuestionUsersBlock
{

    public function count_data()
    {
        $question_cid = $this->getRequest()->query->get(
            Manager::PARAM_QUESTION
        );
        $complex_question = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ComplexContentObjectItem::class, $question_cid
        );

        $reporting_data = new ReportingData();

        $categories = $this->get_assessment_information_headers();
        $categories = array_merge($categories, $this->get_question_information_headers());
        $categories = array_merge($categories, $this->get_question_user_reporting_info_headers());

        $reporting_data->set_categories($categories);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $this->getPublicationId()
        );

        $this->add_category_from_array(
            Translation::get('Details'), $this->get_assessment_information($publication), $reporting_data
        );

        $this->add_category_from_array(
            Translation::get('Details'), $this->get_question_information($complex_question->get_ref_object()),
            $reporting_data
        );

        $user = DataManager::retrieve_by_id(User::class, $this->get_user_id());
        $user_attempts = $this->get_question_attempts_from_publication_and_question(
            $this->getPublicationId(), $question_cid, $this->get_user_id()
        );

        $this->add_category_from_array(
            Translation::get('Details'),
            $this->get_question_user_reporting_info($complex_question, $user, $user_attempts), $reporting_data
        );

        $reporting_data->set_rows([Translation::get('Details')]);

        return $reporting_data;
    }

    public function get_views()
    {
        return [Html::VIEW_TABLE];
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
