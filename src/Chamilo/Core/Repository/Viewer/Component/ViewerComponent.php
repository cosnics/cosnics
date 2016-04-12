<?php
namespace Chamilo\Core\Repository\Viewer\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ViewerComponent extends Manager
{

    public function run()
    {
        $contentObjectIdentifier = $this->getRequest()->query->get(self :: PARAM_VIEW_ID);

        if ($contentObjectIdentifier)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $contentObjectIdentifier);

            $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

            if (RightsService :: getInstance()->canUseContentObject($this->get_user(), $content_object))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Publish'),
                        $this->get_url(
                            array_merge($this->get_parameters(), array(self :: PARAM_ID => $content_object->get_id())),
                            false)));
            }

            if (RightsService :: getInstance()->canEditContentObject($this->get_user(), $content_object) &&
                 RightsService :: getInstance()->canUseContentObject($this->get_user(), $content_object))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('EditAndPublish'),
                        Theme :: getInstance()->getCommonImagePath('Action/Editpublish'),
                        $this->get_url(
                            array_merge(
                                $this->get_parameters(),
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_CREATOR,
                                    self :: PARAM_EDIT_ID => $content_object->get_id())))));
            }

            $html = array();

            $html[] = $this->render_header();
            $html[] = ContentObjectRenditionImplementation :: launch(
                $content_object,
                ContentObjectRendition :: FORMAT_HTML,
                ContentObjectRendition :: VIEW_FULL,
                $this);
            $html[] = $toolbar->as_html();
            $html[] = '<div class="clear"></div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repo_viewer_viewer');
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSER)),
                Translation :: get('BrowserComponent')));
    }
}
