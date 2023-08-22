<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application\calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttachmentViewerComponent extends Manager
{

    /**
     * @throws ParameterNotDefinedException
     * @throws ObjectNotExistException
     * @throws NotAllowedException
     */
    public function run()
    {
        // retrieve the attachment id
        $attachment_id = $this->getRequest()->query->get(self::PARAM_OBJECT);
        if (is_null($attachment_id))
        {
            throw new ParameterNotDefinedException(self::PARAM_OBJECT);
        }

        // retrieve the calendar publication
        $publicationIdentifier = $this->getRequest()->query->get(Manager::PARAM_PUBLICATION_ID);
        if (is_null($publicationIdentifier))
        {
            throw new ParameterNotDefinedException(Manager::PARAM_PUBLICATION_ID);
        }

        $publication = $this->getPublicationService()->findPublicationByIdentifier($publicationIdentifier);
        if (!$publication)
        {
            throw new ObjectNotExistException(Translation::get('Publication'), $publicationIdentifier);
        }

        if (!$this->getRightsService()->isAllowedToViewPublication($publication, $this->getUser()))
        {
            throw new NotAllowedException();
        }

        // is the attachment actually attached to the publication
        if (!$publication->get_publication_object()->is_attached_to_or_included_in($attachment_id))
        {
            throw new NotAllowedException();
        }

        $contentObject = $this->getDataClassRepository()->retrieveById(ContentObject::class, $attachment_id);

        if (!$contentObject)
        {
            throw new ObjectNotExistException(Translation::get('ContentObject'), $attachment_id);
        }

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

        $html = [];

        $html[] = $this->render_header();
        $html[] = ContentObjectRenditionImplementation::launch(
            $contentObject, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL, $this
        );
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param ContentObject $attachment
     *
     * @return string>
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->get_url(
            [Application::PARAM_ACTION => Manager::ACTION_VIEW_ATTACHMENT, 'object' => $attachment->get_id()]
        );
    }
}
