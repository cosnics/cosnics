<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttachmentViewerComponent extends Manager
{

    /**
     *
     * @throws ParameterNotDefinedException
     * @throws ObjectNotExistException
     * @throws NotAllowedException
     */
    public function run()
    {
        // retrieve the attachment id
        $attachment_id = Request :: get(self :: PARAM_OBJECT);
        if (is_null($attachment_id))
        {
            throw new ParameterNotDefinedException(self :: PARAM_OBJECT);
        }

        // retrieve the calendar publication
        $calendar_publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);
        if (is_null($calendar_publication_id))
        {
            throw new ParameterNotDefinedException(Manager :: PARAM_PUBLICATION_ID);
        }

        $calendar_publication = DataManager :: retrieve_by_id(Publication :: class_name(), $calendar_publication_id);
        if (! $calendar_publication)
        {
            throw new ObjectNotExistException(Translation :: get('Publication'), $calendar_publication_id);
        }

        /* are you allowed to view the publication? */

        $user = $this->get_user();

        $is_target = $calendar_publication->is_target($user);
        $is_publisher = ($calendar_publication->get_publisher() == $user->get_id());
        $is_platform_admin = $user->is_platform_admin();

        if (! $is_target && ! $is_publisher && ! $is_platform_admin)
        {
            throw new NotAllowedException();
        }

        // is the attachment actually attached to the publication
        if (! $calendar_publication->get_publication_object()->is_attached_to_or_included_in($attachment_id))
        {
            throw new NotAllowedException();
        }

        $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $attachment_id);
        if (! $object)
        {
            throw new ObjectNotExistException(Translation :: get('ContentObject'), $attachment_id);
        }

        Page :: getInstance()->setViewMode(Page :: VIEW_MODE_HEADERLESS);

        $html = array();

        $html[] = $this->render_header();
        $html[] = ContentObjectRenditionImplementation :: launch(
            $object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FULL,
            $this);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param ContentObject $attachment
     * @return string>
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->get_url(
            array(Application :: PARAM_ACTION => Manager :: ACTION_VIEW_ATTACHMENT, 'object' => $attachment->get_id()));
    }
}
