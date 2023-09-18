<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

class BrowserComponent extends Manager
{

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function get_tool_actions()
    {
        $tool_actions = [];

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {

            $tool_actions[] = new Button(
                Translation::get('Reporting'), new FontAwesomeGlyph('chart-bar'), $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'reporting',
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW,
                    \Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID => 2
                )
            ), Button::DISPLAY_ICON_AND_LABEL
            );
        }

        return $tool_actions;
    }
}
