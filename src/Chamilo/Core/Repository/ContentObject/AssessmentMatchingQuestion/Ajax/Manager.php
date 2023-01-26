<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = 'AssessmentMatchingQuestion';
}
