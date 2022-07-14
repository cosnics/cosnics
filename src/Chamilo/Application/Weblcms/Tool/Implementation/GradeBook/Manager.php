<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\GradeBook;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Service\GradeBookPublicationService;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;

/**
 *
 * @package application.lib.weblcms.tool.gradebook.component
 */

/**
 * This tool allows a user to publish gradebooks in his or her course.
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
        return array(GradeBook::class_name());
    }

    /**
     * @return GradeBookPublicationService
     */
    public function getPublicationService(): GradeBookPublicationService
    {
        return $this->getService(GradeBookPublicationService::class);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getGradeBookPublication(ContentObjectPublication $contentObjectPublication)
    {
        return $this->getPublicationService()->findPublicationByContentObjectPublication($contentObjectPublication);
    }
}
