<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssignmentEntitiesTemplate;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\PublicationRepository;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.tool.assignment.php This tool allows a user to publish assignments in his or her course
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable,
    IntroductionTextSupportInterface
{
    const ACTION_DISPLAY = 'Display';

    // Parameters
    const PARAM_SUBMISSION = 'submission';
    const PARAM_SUBMITTER_TYPE = 'submitter_type';
    const PARAM_TARGET_ID = 'target_id';

    const ACTION_DOWNLOAD_ENTRIES = 'EntriesDownloader';

    // Properties
    const PROPERTY_NUMBER_OF_SUBMISSIONS = 'NumberOfSubmissions';

    /**
     * Constructor.
     *
     * @param ApplicationConfigurationInterface $applicationConfiguration
     *
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if(!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }
    }

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }

    public static function get_allowed_types()
    {
        return array(Assignment::class_name());
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
                Translation::get('BrowseSubmitters'),
                Theme::getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                    )
                ),
                ToolbarItem::DISPLAY_ICON
            ), 0
        );

        $toolbar->insert_item(
            new ToolbarItem(
                Translation::get('Reporting'),
                Theme::getInstance()->getCommonImagePath('Action/Reporting'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'Reporting',
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                        \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID => AssignmentEntitiesTemplate::class
                    )
                ),
                ToolbarItem::DISPLAY_ICON, false, null, '_blank'
            ),
            2
        );

        return $toolbar;
    }

    public function addContentObjectPublicationButtons(
        $publication, ButtonGroup $buttonGroup,
        DropdownButton $dropdownButton
    )
    {
        $buttonGroup->prependButton(
            new Button(
                Translation::get('BrowseSubmitters'),
                new FontAwesomeGlyph('folder-open'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                    )
                ),
                Button::DISPLAY_ICON,
                false,
                'btn-link'
            )
        );
    }

    /**
     * @return null|string
     */
    public function isEphorusEnabled()
    {
        return false;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService
     */
    public function getAssignmentService()
    {
        return $this->getService(AssignmentService::class);
    }

    /**
     * @return EntityServiceManager
     */
    public function getEntityServiceManager()
    {
        return $this->getService(EntityServiceManager::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository()
    {
        return $this->getService(PublicationRepository::class);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getAssignmentPublication(ContentObjectPublication $contentObjectPublication)
    {
        return $this->getPublicationRepository()->findPublicationByContentObjectPublication($contentObjectPublication);
    }
}
