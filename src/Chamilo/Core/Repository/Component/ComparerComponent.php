<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: comparer.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component which can be used to compare a content object.
 */
class ComparerComponent extends Manager
{
    const PARAM_BASE_CONTENT_OBJECT_ID = 'base_content_object_id';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        // $trail = BreadcrumbTrail :: getInstance();
        $object_ids = $this->getRequest()->request->get(self::PARAM_CONTENT_OBJECT_ID);
        if(empty($object_ids))
        {
            $object_ids = $this->getRequest()->query->get(self::PARAM_CONTENT_OBJECT_ID);
        }

        if ($object_ids)
        {
            $object_id = $object_ids[0];
            $version_id = $object_ids[1];
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

        $contentObject = DataManager::retrieve_by_id(ContentObject::class_name(), $object_id);
        $contentObjectVersion = DataManager::retrieve_by_id(ContentObject::class_name(), $version_id);

        if (!$contentObject instanceof ContentObject)
        {
            throw new ObjectNotExistException($contentObjectTranslation, $object_id);
        }

        if (!$contentObjectVersion instanceof ContentObject)
        {
            throw new ObjectNotExistException($contentObjectTranslation, $version_id);
        }

        $isAllowedToViewObject = RightsService::getInstance()->canViewContentObject(
            $this->get_user(),
            $contentObject,
            $this->getWorkspace()
        );

        $isAllowedToViewVersion = RightsService::getInstance()->canViewContentObject(
            $this->get_user(),
            $contentObjectVersion,
            $this->getWorkspace()
        );

        if (!$isAllowedToViewObject || !$isAllowedToViewVersion)
        {
            throw new NotAllowedException();
        }

        if ($contentObject->get_state() == ContentObject::STATE_RECYCLED)
        {
            $this->force_menu_url($this->get_recycle_bin_url());
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->renderComparedVersions($contentObject, $contentObjectVersion);
        $html[] = $contentObject->get_difference($version_id)->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $baseContentObject = $this->getBaseContentObject();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS,
                        self::PARAM_CONTENT_OBJECT_ID => $baseContentObject->getId()
                    ),
                    array(self::PARAM_BASE_CONTENT_OBJECT_ID)
                ),
                Translation::get('ViewContentObject', array('CONTENT_OBJECT' => $baseContentObject->get_title()))
            )
        );

        $breadcrumbtrail->add_help('repository_comparer');
    }

    public function get_additional_parameters($additionalParameters = array())
    {
        return array(
            self::PARAM_BASE_CONTENT_OBJECT_ID, self::PARAM_CONTENT_OBJECT_ID, self::PARAM_COMPARE_OBJECT,
            self::PARAM_COMPARE_VERSION
        );
    }

    /**
     * Renders the compared versions
     */
    protected function renderComparedVersions(ContentObject $contentObject, ContentObject $contentObjectVersion)
    {
        $html = array();

        $html[] = '<table class="table table-bordered table-striped comparer-header-table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';
        $html[] = '<th>' . Translation::getInstance()->getTranslation('OldVersion') . '</th>';
        $html[] = '<th>' . Translation::getInstance()->getTranslation('NewVersion') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        $html[] = '<tr>';
        $html[] = '<th class="comparer-header-title">' . Translation::getInstance()->getTranslation('Title') . '</th>';
        $html[] = '<td>' . $contentObject->get_title() . '</td>';
        $html[] = '<td>' . $contentObjectVersion->get_title() . '</td>';
        $html[] = '</tr>';

        if($contentObject->get_comment() || $contentObjectVersion->get_comment())
        {
            $html[] = '<tr>';
            $html[] = '<th class="comparer-header-title">' . Translation::getInstance()->getTranslation('VersionComment') . '</th>';
            $html[] = '<td>' . $contentObject->get_comment() . '</td>';
            $html[] = '<td>' . $contentObjectVersion->get_comment() . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the base content object
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     *
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

        $contentObject = DataManager::retrieve_by_id(ContentObject::class_name(), $baseContentObjectId);

        if (!$contentObject instanceof ContentObject)
        {
            throw new ObjectNotExistException($contentObjectTranslation, $baseContentObjectId);
        }

        return $contentObject;
    }
}
