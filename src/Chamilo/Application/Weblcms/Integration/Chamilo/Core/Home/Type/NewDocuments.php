<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * A notification block for new document submissions (document tool) based on new_assignments.class.php
 *
 * @author Parcifal Aertssen (Howest)
 */
class NewDocuments extends NewBlock
{

    public function display_content()
    {
        $publications = $this->get_content(self :: TOOL_DOCUMENT);
        $html = $this->display_new_items($publications);

        if (count($html) < 3)
        {
            return Translation :: get('NoNewDocumentsSinceLastVisit');
        }
        return implode(PHP_EOL, $html);
    }

    public function display_new_items($publications)
    {
        ksort($publications);
        $icon = '<img src="' . $this->get_new_documents_icon() . '"/>';

        $html = array();
        $html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
        $current_course_id = - 1;
        foreach ($publications as $publication)
        {
            $course_id = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
            $id = $publication[ContentObjectPublication :: PROPERTY_ID];
            $title = $publication[ContentObject :: PROPERTY_TITLE];

            if ($course_id != $current_course_id)
            {
                $current_course_id = $course_id;
                $html[] = '<li>' . $this->get_course_by_id($current_course_id)->get_title() . '</li>';
            }

            $parameters = array(
                \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE => $course_id,
                Application :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE,
                Application :: PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager :: context(),
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => 'document',
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager :: ACTION_VIEW_DOCUMENTS,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE => ContentObjectRenderer :: TYPE_TABLE,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $id);
            $link = Redirect :: get_link($parameters);

            $html[] = '<a href="' . $link . '">' . $icon . ' ' . $title . '</a><br />';
        }
        $html[] = '</ul>';
        return $html;
    }

    private function get_new_documents_icon()
    {
        return Theme :: getInstance()->getImagePath(
            \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace(self :: TOOL_DOCUMENT),
            'Logo/' . Theme :: ICON_MINI . '_new');
    }
}
