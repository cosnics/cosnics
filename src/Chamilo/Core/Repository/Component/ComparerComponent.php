<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\ContentObjectDifferenceRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component which can be used to compare a content object.
 */
class ComparerComponent extends Manager
{
    public const PARAM_BASE_CONTENT_OBJECT_ID = 'base_content_object_id';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        // $trail = BreadcrumbTrail::getInstance();
        $object_ids = $this->getRequest()->request->get(self::PARAM_CONTENT_OBJECT_ID);
        if (empty($object_ids))
        {
            $object_ids = $this->getRequest()->query->get(self::PARAM_CONTENT_OBJECT_ID);
        }

        if ($object_ids)
        {
            $object_id = $object_ids[1];
            $version_id = $object_ids[0];
        }
        else
        {
            $object_id = Request::get(self::PARAM_COMPARE_OBJECT);
            $version_id = Request::get(self::PARAM_COMPARE_VERSION);
        }

        $contentObjectTranslation = Translation::getInstance()->getTranslation('ContentObject');

        if (empty($object_id) || empty($version_id))
        {
            throw new NoObjectSelectedException($contentObjectTranslation);
        }

        $contentObject = DataManager::retrieve_by_id(ContentObject::class, $object_id);
        $contentObjectVersion = DataManager::retrieve_by_id(ContentObject::class, $version_id);

        if (!$contentObject instanceof ContentObject)
        {
            throw new ObjectNotExistException($contentObjectTranslation, $object_id);
        }

        if (!$contentObjectVersion instanceof ContentObject)
        {
            throw new ObjectNotExistException($contentObjectTranslation, $version_id);
        }

        $isAllowedToViewObject = $this->getWorkspaceRightsService()->canViewContentObject(
            $this->get_user(), $contentObject, $this->getWorkspace()
        );

        $isAllowedToViewVersion = $this->getWorkspaceRightsService()->canViewContentObject(
            $this->get_user(), $contentObjectVersion, $this->getWorkspace()
        );

        if (!$isAllowedToViewObject || !$isAllowedToViewVersion)
        {
            throw new NotAllowedException();
        }

        if ($contentObject->get_state() == ContentObject::STATE_RECYCLED)
        {
            $this->force_menu_url($this->get_recycle_bin_url());
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getContentObjectDifferenceRenderer()->render($contentObject->get_difference($version_id));
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $baseContentObject = $this->getBaseContentObject();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS,
                        self::PARAM_CONTENT_OBJECT_ID => $baseContentObject->getId()
                    ], [self::PARAM_BASE_CONTENT_OBJECT_ID]
                ), Translation::get('ViewContentObject', ['CONTENT_OBJECT' => $baseContentObject->get_title()])
            )
        );
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BASE_CONTENT_OBJECT_ID;
        $additionalParameters[] = self::PARAM_CONTENT_OBJECT_ID;
        $additionalParameters[] = self::PARAM_COMPARE_OBJECT;
        $additionalParameters[] = self::PARAM_COMPARE_VERSION;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * Returns the base content object
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws NoObjectSelectedException
     * @throws ObjectNotExistException
     */
    protected function getBaseContentObject()
    {
        $baseContentObjectId = $this->getRequest()->get(self::PARAM_BASE_CONTENT_OBJECT_ID);

        $contentObjectTranslation = Translation::getInstance()->getTranslation('ContentObject');

        if (empty($baseContentObjectId))
        {
            throw new NoObjectSelectedException($contentObjectTranslation);
        }

        $contentObject = DataManager::retrieve_by_id(ContentObject::class, $baseContentObjectId);

        if (!$contentObject instanceof ContentObject)
        {
            throw new ObjectNotExistException($contentObjectTranslation, $baseContentObjectId);
        }

        return $contentObject;
    }

    /**
     * @return \Chamilo\Core\Repository\Common\ContentObjectDifferenceRenderer
     */
    protected function getContentObjectDifferenceRenderer()
    {
        return $this->getService(ContentObjectDifferenceRenderer::class);
    }
}
