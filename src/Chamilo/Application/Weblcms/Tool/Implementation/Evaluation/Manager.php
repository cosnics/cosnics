<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation;

use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceManager;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;

/**
 *
 * @package application.lib.weblcms.tool.evaluation.component
 */

/**
 * This tool allows a user to publish evaluations in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable,
    IntroductionTextSupportInterface
{
    const ACTION_AJAX = 'Ajax';
    const ACTION_IMPORT_FROM_CURIOS = 'ImportFromCurios';
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
        return array(Evaluation::class_name());
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository()
    {
        return $this->getService('chamilo.application.weblcms.tool.implementation.evaluation.storage.repository.publication_repository');
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getEvaluationPublication(ContentObjectPublication $contentObjectPublication)
    {
        return $this->getPublicationRepository()->findPublicationByContentObjectPublication($contentObjectPublication);
    }

    /**
     * @return PublicationEntityServiceManager
     */
    public function getPublicationEntityServiceManager()
    {
        return $this->getService(PublicationEntityServiceManager::class);
    }
}
