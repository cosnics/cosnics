<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.weblcms.php.reporting.blocks Reporting block displaying information about the assessment and the
 *          user
 * @author  Joris Willems <joris.willems@gmail.com>
 * @author  Alexander Van Paemel
 */
class AssessmentQuestionInformationBlock extends AssessmentQuestionsBlock
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
        $categories = array_merge($categories, $this->get_question_reporting_info_headers());

        $reporting_data->set_categories($categories);

        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $this->getPublicationId()
        );

        $this->add_category_from_array(
            Translation::get('Details'), $this->get_assessment_information($publication), $reporting_data
        );

        $this->add_category_from_array(
            Translation::get('Details'), $this->get_question_information($complex_question->get_ref_object()),
            $reporting_data
        );

        $this->add_category_from_array(
            Translation::get('Details'), $this->get_question_reporting_info($complex_question), $reporting_data
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
