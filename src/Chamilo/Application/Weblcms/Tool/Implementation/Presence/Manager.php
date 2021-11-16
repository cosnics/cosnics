<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Service\PublicationService;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;

/**
 *
 * @package application.lib.weblcms.tool.presence.component
 */

/**
 * This tool allows a user to publish presences in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable,
    IntroductionTextSupportInterface
{
    const ACTION_DISPLAY = 'Display';

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }

    public static function get_allowed_types()
    {
        return array(Presence::class_name());
    }

    /**
     * @return PublicationService
     */
    public function getPublicationService(): PublicationService
    {
        return $this->getService(PublicationService::class);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getPresencePublication(ContentObjectPublication $contentObjectPublication)
    {
        return $this->getPublicationService()->findPublicationByContentObjectPublication($contentObjectPublication);
    }
}
