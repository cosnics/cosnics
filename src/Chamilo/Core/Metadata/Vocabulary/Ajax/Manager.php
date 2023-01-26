<?php
namespace Chamilo\Core\Metadata\Vocabulary\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\User\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{

    public const ACTION_SELECT = 'Select';
    public const ACTION_VOCABULARY = 'Vocabulary';

    public const CONTEXT = __NAMESPACE__;

    public const PARAM_ELEMENT_IDENTIFIER = 'elementIdentifier';
}
