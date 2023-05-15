<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Reporting;

use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.reporting
 * @author  Michael Kyndt
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    public const ACTION_VIEW_REPORT = 'Viewer';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_VIEW_REPORT;

    public const PARAM_QUESTION = 'question';
    public const PARAM_REPORTING_TOOL = 'reporting_tool';

    /**
     * Adds a breadcrumb to the browser component
     *
     * @param BreadcrumbTrail $breadcrumbTrail
     */
    protected function addBrowserBreadcrumb(BreadcrumbTrail $breadcrumbTrail)
    {
        $filter = [
            \Chamilo\Application\Weblcms\Manager::PARAM_USERS,
            \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID,
            \Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID
        ];

        $breadcrumbTrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_VIEW_REPORT], $filter),
                Translation::getInstance()->getTranslation('ViewerComponent', [], __NAMESPACE__)
            )
        );
    }
}
