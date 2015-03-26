<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Reporting;

/**
 * $Id: reporting_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.reporting
 * @author Michael Kyndt
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const PARAM_REPORTING_TOOL = 'reporting_tool';
    const PARAM_QUESTION = 'question';
    const ACTION_VIEW_REPORT = 'Viewer';
    const DEFAULT_ACTION = self :: ACTION_VIEW_REPORT;
}
