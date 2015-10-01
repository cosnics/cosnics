<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Tool;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;

class LastAccessToToolsPlatformBlock extends ToolAccessBlock
{

    /**
     * Returns the summary data for this course
     * 
     * @return RecordResultSet
     */
    public function retrieve_course_summary_data()
    {
        return WeblcmsTrackingDataManager :: retrieve_tools_access_summary_data();
    }
}
