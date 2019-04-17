<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Translation;
use Hogent\Extension\Chamilo\Application\Weblcms\Rights\WeblcmsRights;

/**
 *
 * @package application.lib.weblcms.tool.external_tool.component
 */

/**
 * This tool allows a user to publish external_tools in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable,
    IntroductionTextSupportInterface
{
    const ACTION_DISPLAY = 'display';

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }

    /**
     * Adds extra actions to the toolbar in different components
     *
     * @param $toolbar Toolbar
     * @param array $publication
     *
     * @return Toolbar
     */
    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $toolbar->insert_item(
            new ToolbarItem(
                $this->getTranslator()->trans('BrowseSubmitters', [], Manager::context()),
                $this->getThemeUtilities()->getCommonImagePath('Action/Browser'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                    )
                ),
                ToolbarItem::DISPLAY_ICON
            ), 0
        );

        return $toolbar;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getContentObjectPublication()
    {
        if(!$this->contentObjectPublication instanceof ContentObjectPublication)
        {
            $publicationId = $this->getRequest()->getFromUrl(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
            );

            $this->contentObjectPublication = $this->getPublicationService()->getPublication($publicationId);

            if (!$this->contentObjectPublication instanceof ContentObjectPublication)
            {
                throw new ObjectNotExistException(Translation::get('Publication'), $publicationId);
            }
        }

        return $this->contentObjectPublication;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function validateAccess(ContentObjectPublication $contentObjectPublication)
    {
        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $contentObjectPublication))
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @return \Chamilo\Application\Weblcms\Service\PublicationService
     */
    public function getPublicationService()
    {
        return $this->getService(PublicationService::class);
    }

    /**
     * @return array
     */
    public static function get_allowed_types()
    {
        return array(ExternalTool::class_name());
    }
}
