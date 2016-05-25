<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SkipOptionComponent extends \Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Ajax\Manager
{
    const PARAM_OPTION_NUMBER = 'option-number';

    /**
     *
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_OPTION_NUMBER);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $_SESSION['select_skip_options'][] = $this->getPostDataValue(self::PARAM_OPTION_NUMBER);
        JsonAjaxResult::success();
    }
}