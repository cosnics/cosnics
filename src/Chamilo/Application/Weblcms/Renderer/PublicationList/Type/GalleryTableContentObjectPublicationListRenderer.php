<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Table\Publication\Gallery\ObjectPublicationGalleryTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\NotificationMessage;
use Chamilo\Libraries\Platform\Translation;

/**
 * Renderer to display a sortable table with learning object publications.
 *
 * @author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GalleryTableContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer implements
    TableSupport
{

    public function __construct($tool_browser, $parameters = array())
    {
        parent :: __construct($tool_browser, $parameters);
        $this->addWarning();
    }

    /**
     * Returns the HTML output of this renderer.
     *
     * @return string The HTML output
     */
    public function as_html()
    {
        if (method_exists($this->get_tool_browser()->get_parent(), 'get_content_object_publication_gallery_table'))
        {
            $table = $this->get_tool_browser()->get_parent()->get_content_object_publication_gallery_table($this);
        }
        else
        {
            $table = new ObjectPublicationGalleryTable($this);
        }

        return $table->as_html();
    }

    public function addWarning()
    {
        $messages = Session :: retrieve(Application :: PARAM_MESSAGES);
        $messages[Application :: PARAM_MESSAGE_TYPE][] = NotificationMessage :: TYPE_WARNING;
        $messages[Application :: PARAM_MESSAGE][] = Translation :: get('BrowserWarningPreview');

        Session :: register(Application :: PARAM_MESSAGES, $messages);
    }

    /**
     * Returns the parameters that the table needs for the url building
     *
     * @return string[]
     */
    public function get_parameters()
    {
        return $this->get_tool_browser()->get_parameters();
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        return $this->get_publication_conditions();
    }
}
