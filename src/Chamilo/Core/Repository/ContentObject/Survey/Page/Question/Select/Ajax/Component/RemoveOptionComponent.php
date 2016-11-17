<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Ajax\Component;

/**
 *
 * @package repository.content_object.survey;
 */
class RemoveOptionComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Ajax\Manager
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
        $_SESSION['mq_skip_options'][] = $option_id;
    }
}
?>