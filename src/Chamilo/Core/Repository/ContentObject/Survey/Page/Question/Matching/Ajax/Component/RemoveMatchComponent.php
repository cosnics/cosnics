<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Ajax\Component;

use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package repository.content_object.survey_matching_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class RemoveMatchComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Ajax\Manager
{
    const PARAM_MATCH_ID = 'match_id';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_MATCH_ID);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $match_id = $this->getPostDataValue(self :: PARAM_MATCH_ID);
        
        $mq_skip_matches = Session :: retrieve('mq_skip_matches');
        
        if (! is_array($mq_skip_matches))
        {
            $mq_skip_matches = array();
        }
        
        $mq_skip_matches[] = $match_id;
        
        Session :: register('mq_skip_matches', $mq_skip_matches);
    }
}
?>