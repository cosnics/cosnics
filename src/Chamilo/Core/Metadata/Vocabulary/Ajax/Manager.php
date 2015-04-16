<?php
namespace Chamilo\Core\Metadata\Vocabulary\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Core\User\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const ACTION_VOCABULARY = 'Vocabulary';
    const ACTION_SELECT = 'Select';
    const PARAM_ELEMENT_IDENTIFIER = 'elementIdentifier';
}
