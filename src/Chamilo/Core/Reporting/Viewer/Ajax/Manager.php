<?php
namespace Chamilo\Core\Reporting\Viewer\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Core\Reporting\Viewer\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const PARAM_GRAPHMD5 = 'graphMd5';
    const ACTION_GRAPH = 'Graph';
}
