<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying information about the assessment and
 *          access details
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentInformationBlock extends AssessmentsBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $categories = $this->get_assessment_information_headers();
        $categories = array_merge($categories, $this->get_assessment_reporting_info_headers());
        
        $reporting_data->set_categories($categories);
        
        $publication_id = $this->getPublicationId();
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class,
            $publication_id);
        
        $this->add_category_from_array(
            Translation::get('Details'), 
            $this->get_assessment_information($publication), 
            $reporting_data);
        
        $reporting_data->set_rows(array(Translation::get('Details')));
        
        $assessment_reporting_info = $this->get_assessment_reporting_info($publication);
        foreach ($assessment_reporting_info as $translation => $value)
        {
            $reporting_data->add_data_category_row($translation, Translation::get('Details'), $value);
        }
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }
}
