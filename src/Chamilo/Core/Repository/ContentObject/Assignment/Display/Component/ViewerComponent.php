<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewerComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        return $this->getTwig()->render(Manager::context() . ':EntityBrowser.html.twig', $this->getTemplateProperties());
    }

    /**
     *
     * @return string[]
     */
    protected function getTemplateProperties()
    {
        $entityName = $this->getDataProvider()->getEntityNameByType($this->getEntityType());
        $entryCount = $this->getDataProvider()->countDistinctEntriesByEntityType($this->getEntityType());
        $feedbackCount = $this->getDataProvider()->countDistinctFeedbackByEntityType($this->getEntityType());
        $lateEntryCount = $this->getDataProvider()->countDistinctLateEntriesByEntityType($this->getEntityType());
        $entityCount = $this->getDataProvider()->countEntitiesByEntityType($this->getEntityType());

        /** @var Assignment $assignment */
        $assignment = $this->get_root_content_object();

        $startTime = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
            $assignment->get_start_time()
        );

        $endTime = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
            $assignment->get_end_time()
        );

        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'BUTTON_TOOLBAR' => $this->getButtonToolbarRenderer()->render(),
            'CONTENT_OBJECT_RENDITION' => $this->renderContentObject(),
            'ENTITY_NAME' => $entityName, 'ENTITY_COUNT' => $entityCount, 'ENTRY_COUNT' => $entryCount,
            'FEEDBACK_COUNT' => $feedbackCount, 'LATE_ENTRY_COUNT' => $lateEntryCount,
            'START_TIME' => $startTime, 'END_TIME' => $endTime,
            'ALLOW_LATE_SUBMISSIONS' => $assignment->get_allow_late_submissions(),
            'ALLOW_GROUP_SUBMISSIONS' => $assignment->get_allow_group_submissions(),
            'VISIBILITY_SUBMISSIONS' => $assignment->get_visibility_submissions(),
            'ENTITY_TABLE' => $this->renderEntityTable()
        ];
    }

    /**
     *
     * @return string
     */
    protected function renderContentObject()
    {
        $display = ContentObjectRenditionImplementation::factory(
            $this->get_root_content_object(),
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_FULL,
            $this
        );

        return $display->render();
    }

    /**
     *
     * @return string
     */
    protected function renderEntityTable()
    {
        return $this->getDataProvider()->getEntityTableForType($this, $this->getEntityType())->as_html();
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableSupport::get_table_condition()
     */
    public function get_table_condition($tableClassName)
    {
        // TODO Auto-generated method stub
    }

    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolBar = new ButtonToolBar();
            $buttonToolBar->addButtonGroup(
                new ButtonGroup(
                    array(
                        new Button(
                            Translation::get('Download'),
                            Theme::getInstance()->getCommonImagePath('Action/Download'),
                            $this->get_url([self::PARAM_ACTION => self::ACTION_DOWNLOAD])
                        ),
//                        new Button(
//                            Translation::get('SubmissionSubmit'),
//                            Theme::getInstance()->getCommonImagePath('Action/Add'),
//                            $this->get_url(
//                                [
//                                    self::PARAM_ACTION => self::ACTION_CREATE,
//                                    self::PARAM_ENTITY_TYPE => $this->getEntityType(),
//                                    self::PARAM_ENTITY_ID => $this->getEntityIdentifier()
//                                ]
//                            )
//                        )
                    )
                )
            );

//            $buttonToolBar->addButtonGroup(
//                new ButtonGroup(
//                    array(
//                        new Button(
//                            Translation::get('ScoreOverview'),
//                            Theme::getInstance()->getCommonImagePath('Action/Statistics')
//                        ),
//                        new Button(
//                            Translation::get('EntriesOverview'),
//                            Theme::getInstance()->getCommonImagePath('Action/Statistics')
//                        )
//                    )
//                )
//            );

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        }

        return $this->buttonToolbarRenderer;
    }
}
