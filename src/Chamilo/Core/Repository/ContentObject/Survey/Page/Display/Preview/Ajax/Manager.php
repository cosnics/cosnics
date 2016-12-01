<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface
     */
    private $applicationConfiguration;
    const TEMPORARY_STORAGE = 'survey_page_preview';
    const PARAM_COMPLEX_QUESTION_ID = 'complex_question_id';
    const PARAM_PARAMETERS = 'parameters';
    const PARAM_RESULT = 'result';
    const PARAM_ANSWER = 'answer';
    const PARAM_ANSWER_ID = 'answer_id';
    const PARAM_ANSWER_VALUE = 'answer_value';
    const PARAM_QUESTION_VISIBILITY = 'question_visibility';
}
