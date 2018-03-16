<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\AssignmentRepository;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentDataProvider;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\CourseGroupEntityService;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\PlatformGroupEntityService;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\UserEntityService;

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

    // Properties
    const PROPERTY_NUMBER_OF_SUBMISSIONS = 'NumberOfSubmissions';

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
                Translation::get('SubmissionSubmit'),
                Theme::getInstance()->getCommonImagePath('Action/Add'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_CREATE
                    )
                ),
                ToolbarItem::DISPLAY_ICON
            ),
            1
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

        $buttonGroup->prependButton(
            new Button(
                Translation::get('SubmissionSubmit'),
                new FontAwesomeGlyph('plus'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_CREATE
                    )
                ),
                Button::DISPLAY_ICON,
                false,
                'btn-link'
            )
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    public function getAssignmentService()
    {
        return $this->getService('chamilo.application.weblcms.integration.chamilo.core.tracking.service.assignment_service');
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentDataProvider
     */
    public function getAssignmentDataProvider()
    {
        return $this->getService('chamilo.application.weblcms.tool.implementation.assignment.service.assignment_data_provider');
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository()
    {
        return $this->getService('chamilo.application.weblcms.tool.implementation.assignment.storage.repository.publication_repository');
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
}
