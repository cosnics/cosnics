<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssignmentEntitiesTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentDataProvider;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.tool.assignment.php This tool allows a user to publish assignments in his or her course
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
    implements Categorizable, IntroductionTextSupportInterface
{
    const ACTION_DISPLAY = 'Display';

    // Parameters

    const ACTION_DOWNLOAD_ENTRIES = 'EntriesDownloader';

    const PARAM_SUBMISSION = 'submission';

    const PARAM_SUBMITTER_TYPE = 'submitter_type';

    const PARAM_TARGET_ID = 'target_id';

    // Properties

    const PROPERTY_NUMBER_OF_SUBMISSIONS = 'NumberOfSubmissions';

    public function addContentObjectPublicationButtons(
        $publication, ButtonGroup $buttonGroup, DropdownButton $dropdownButton
    )
    {
        $buttonGroup->prependButton(
            new Button(
                Translation::get('BrowseSubmitters'), new FontAwesomeGlyph('folder-open'), $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                )
            ), Button::DISPLAY_ICON, false, 'btn-link'
            )
        );

        $buttonGroup->prependButton(
            new Button(
                Translation::get('SubmissionSubmit'), new FontAwesomeGlyph('plus'), $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_CREATE
                )
            ), Button::DISPLAY_ICON, false, 'btn-link'
            )
        );

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication) && $this->isEphorusEnabled())
        {
            $glyph = new NamespaceIdentGlyph(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager::context(), false, false, false,
                IdentGlyph::SIZE_MINI
            );

            $buttonGroup->prependButton(
                new Button(
                    Translation::get('EphorusOverview'), $glyph, $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_EPHORUS
                    )
                ), Button::DISPLAY_ICON, false, null, '_blank'
                )
            );
        }
    }

    /**
     * Adds extra actions to the toolbar in different components
     *
     * @param $toolbar Toolbar
     * @param $publication Publication
     *
     * @return Toolbar
     */
    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $toolbar->insert_item(
            new ToolbarItem(
                Translation::get('BrowseSubmitters'), new FontAwesomeGlyph('folder'), $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                )
            ), ToolbarItem::DISPLAY_ICON
            ), 0
        );

        $toolbar->insert_item(
            new ToolbarItem(
                Translation::get('SubmissionSubmit'), new FontAwesomeGlyph('plus'), $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_CREATE
                )
            ), ToolbarItem::DISPLAY_ICON
            ), 1
        );

        $toolbar->insert_item(
            new ToolbarItem(
                Translation::get('Reporting'), new FontAwesomeGlyph('chart-pie'), $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'Reporting',
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                    \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID => AssignmentEntitiesTemplate::class
                )
            ), ToolbarItem::DISPLAY_ICON, false, null, '_blank'
            ), 2
        );

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication) && $this->isEphorusEnabled())
        {
            $glyph = new NamespaceIdentGlyph(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager::context(), false, false, false,
                IdentGlyph::SIZE_MINI
            );

            $toolbar->insert_item(
                new ToolbarItem(
                    Translation::get('EphorusOverview'), $glyph, $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_EPHORUS
                    )
                ), ToolbarItem::DISPLAY_ICON, false, null, '_blank'
                ), 3
            );
        }

        return $toolbar;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentDataProvider
     */
    public function getAssignmentDataProvider()
    {
        return $this->getService(AssignmentDataProvider::class);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getAssignmentPublication(ContentObjectPublication $contentObjectPublication)
    {
        return $this->getPublicationRepository()->findPublicationByContentObjectPublication($contentObjectPublication);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    public function getAssignmentService()
    {
        return $this->getService(AssignmentService::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository()
    {
        return $this->getService(PublicationRepository::class);
    }

    public static function get_allowed_types()
    {
        return array(Assignment::class_name());
    }

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }

    /**
     * @return null|string
     */
    public function isEphorusEnabled()
    {
        $ephorusToolRegistration = DataManager::retrieve_course_tool_by_name('Ephorus');

        if (!$ephorusToolRegistration)
        {
            return false;
        }

        $toolActive = CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(), CourseSetting::COURSE_SETTING_TOOL_ACTIVE, $ephorusToolRegistration->get_id()
        );

        return $toolActive;
    }
}
