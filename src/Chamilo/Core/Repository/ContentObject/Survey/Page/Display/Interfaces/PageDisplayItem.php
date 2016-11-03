<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplayItem;

interface PageDisplayItem extends SurveyDisplayItem
{
    
    /**
     * @param string $prefix
     * @return mixed
     */
    public function getAnswerIds($prefix = null);
    
}

?>