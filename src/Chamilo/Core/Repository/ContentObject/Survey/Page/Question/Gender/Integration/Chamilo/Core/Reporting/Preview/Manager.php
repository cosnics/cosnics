<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Integration\Chamilo\Core\Reporting\Preview;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Integration\Chamilo\Core\Reporting\Template\TableTemplate;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Reporting\Preview\Manager
{
    // Actions
    const ACTION_TABLE = 'table';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_TABLE;

    // Url Creation
    function get_viewer_url($question_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_TABLE, TableTemplate :: PARAM_QUESTION_ID => $question_id));
    }

    /**
     *
     * @return multitype:string
     */
    static public function get_available_actions()
    {
        return array(self :: ACTION_TABLE);
    }
}
?>