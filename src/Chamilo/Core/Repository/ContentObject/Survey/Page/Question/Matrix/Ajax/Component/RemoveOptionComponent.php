<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Ajax\Component;

use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class RemoveOptionComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Ajax\Manager
{
    const PARAM_OPTION_ID = 'option_id';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    function getRequiredPostParameters()
    {
        return array(self::PARAM_OPTION_ID);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $option_id = $this->getPostDataValue(self::PARAM_OPTION_ID);
        
        $mq_skip_options = Session::retrieve('mq_skip_options');
        
        if (! is_array($mq_skip_options))
        {
            $mq_skip_options = array();
        }
        
        $mq_skip_options[] = $option_id;
        
        Session::register('mq_skip_options', $mq_skip_options);
    }
}