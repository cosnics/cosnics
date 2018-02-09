<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
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

    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        $this->checkAccessRights();

        return $this->getTwig()->render(
            Manager::context() . ':EntityBrowser.html.twig', $this->getTemplateProperties()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if(!$this->getRightsService()->canUserViewEntityBrowser($this->getUser(), $this->getAssignment()))
        {
            throw new NotAllowedException();
        }

    }

    /**
     *
     * @return string[]
     */
    protected function getTemplateProperties()
    {
        $entityName = $this->getDataProvider()->getPluralEntityNameByType($this->getEntityType());
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
            'CONTENT_OBJECT_TITLE' => $this->get_root_content_object()->get_title(),
            'CONTENT_OBJECT_RENDITION' => $this->renderContentObject(),
            'ENTITY_NAME' => $entityName, 'ENTITY_COUNT' => $entityCount, 'ENTRY_COUNT' => $entryCount,
            'FEEDBACK_COUNT' => $feedbackCount, 'LATE_ENTRY_COUNT' => $lateEntryCount,
            'START_TIME' => $startTime, 'END_TIME' => $endTime,
            'ALLOW_LATE_SUBMISSIONS' => $assignment->get_allow_late_submissions(),
            'ALLOW_GROUP_SUBMISSIONS' => $assignment->get_allow_group_submissions(),
            'VISIBILITY_SUBMISSIONS' => $assignment->get_visibility_submissions(),
            'ENTITY_TABLE' => $this->renderEntityTable(),
            'CAN_EDIT_ASSIGNMENT' => $this->getDataProvider()->canEditAssignment(),
            'ADMINISTRATOR_EMAIL' => $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'administrator_email'])
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
            ContentObjectRendition::VIEW_DESCRIPTION,
            $this
        );

        return $display->render();
    }

    public function get_content_object_display_attachment_url(
        $attachment
    )
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_ATTACHMENT,
                self::PARAM_ATTACHMENT_ID => $attachment->get_id()
            )
        );
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
                            Translation::get('DownloadAll'),
                            new FontAwesomeGlyph('download'),
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
