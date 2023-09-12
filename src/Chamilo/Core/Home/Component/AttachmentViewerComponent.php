<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\PageConfiguration;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttachmentViewerComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        $trail = $this->getBreadcrumbTrail();
        $translator = $this->getTranslator();

        $failed = false;
        $error_message = '';

        // retrieve the parent content object
        $parent_id = $this->getRequest()->query->get(self::PARAM_PARENT_ID);

        if (is_null($parent_id))
        {
            throw new ParameterNotDefinedException(self::PARAM_PARENT_ID);
        }
        $parent = DataManager::retrieve_by_id(ContentObject::class, $parent_id);

        if (is_null($parent))
        {
            throw new ObjectNotExistException($translator->trans('Object', [], 'Chamilo\Core\Repository'), $parent);
        }

        // retrieve the attachment
        $object_id = $this->getRequest()->query->get(self::PARAM_OBJECT_ID);

        if (is_null($object_id))
        {
            $failed = true;
            $error_message = $translator->trans('NoAttachmentSelected', [], 'Chamilo\Core\Repository');
        }

        $object = DataManager::retrieve_by_id(ContentObject::class, $object_id);

        if (is_null($object))
        {
            throw new ObjectNotExistException($translator->trans('Attachment'), $object);
        }

        // Default the attachment is attached to the content object of the
        // publication
        if (!$parent->is_attached_to_or_included_in($object_id))

        {
            $failed = true;
            $error_message = $translator->trans('WrongObjectSelected', [], 'Chamilo\Core\Repository');
        }

        $html = [];

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

        if (!$failed)
        {
            $trail->add(
                new Breadcrumb(
                    $this->get_url(['object' => $parent_id]),
                    $translator->trans('ViewAttachment', [], \Chamilo\Core\Repository\Manager::CONTEXT)
                )
            );

            $html[] = $this->renderHeader();

            $html[] = ContentObjectRenditionImplementation::launch(
                $object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL
            );
        }
        else
        {
            $html[] = $this->renderHeader();
            $html[] = $this->display_error_message($error_message);
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function get_content_object_display_attachment_url(ContentObject $attachment): ?string
    {
        $object_id = $this->getRequest()->query->get(self::PARAM_OBJECT_ID);
        $object = DataManager::retrieve_by_id(ContentObject::class, $object_id);

        if (!$this->is_view_attachment_allowed($object))
        {
            return null;
        }

        return $this->get_url(
            [self::PARAM_PARENT_ID => $object_id, self::PARAM_OBJECT_ID => $attachment->getId()]
        );
    }

    /**
     * Determines whether the object may be viewed by the current user.
     *
     * @param ContentObject $object The content object to be tested.
     *
     * @return bool Whether the current user may view the content object.
     */
    public function is_view_attachment_allowed(ContentObject $object): bool
    {
        // Is the current user the owner?
        return $object->get_owner_id() == $this->getUser()->getId();
    }
}
