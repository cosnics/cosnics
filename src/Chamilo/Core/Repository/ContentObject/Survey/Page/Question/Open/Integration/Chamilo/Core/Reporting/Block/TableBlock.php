<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;

class TableBlock extends ReportingBlock
{
    const ANSWER = 'answer';

    public function count_data()
    {
        $question = $this->get_parent()->get_question();
        $answers = $this->get_parent()->get_answers($question->get_id());

        $reporting_data = new ReportingData();

        $reporting_data->add_category(Translation :: get(self :: ANSWER));
        $stripped_answers = array();
        foreach ($answers as $text)
        {
            if (strlen(strip_tags($text)) > 0)
            {
                $stripped_answers[] = strip_tags($text);
            }
        }

        $answer_count = count($stripped_answers);

        $categories = array();
        $nr = 0;
        while ($answer_count > 0)
        {
            $nr ++;
            $categories[] = $nr;
            $answer_count --;
        }

        $answer_row = Translation :: get(self :: ANSWER);
        $rows = array($answer_row);

        $reporting_data->set_categories($categories);
        $reporting_data->set_rows($rows);
        $nr = 0;

        foreach ($stripped_answers as $answer)
        {
            $nr ++;
            $reporting_data->add_data_category_row($nr, $answer_row, $answer);
        }

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_CSV,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_XLSX,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_XML);
    }
}
