<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class WikiPageCreatorComponent extends Manager implements ViewerInterface, DelegateComponent
{

    public function run()
    {
        if (!\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );
            $component->set_parameter(self::PARAM_ACTION, self::ACTION_CREATE_PAGE);

            return $component->run();
        }
        else
        {
            $objects = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();

            if (!is_array($objects))
            {
                $objects = array($objects);
            }

            foreach ($objects as $object)
            {
                $complex_content_object_item = ComplexContentObjectItem::factory(ComplexWikiPage::class);
                $complex_content_object_item->set_ref($object);
                $complex_content_object_item->set_parent($this->get_root_content_object()->get_id());
                $complex_content_object_item->set_user_id($this->get_user_id());
                $complex_content_object_item->set_display_order(
                    DataManager::select_next_display_order(
                        $this->get_root_content_object()->get_id()
                    )
                );
                $complex_content_object_item->set_is_homepage(0);
                $complex_content_object_item->create();
            }

            $this->redirectWithMessage(
                Translation::get('WikiItemCreated'), '', array(
                    self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id()
                )
            );
        }
    }

    public function get_allowed_content_object_types()
    {
        return $this->get_root_content_object()->get_allowed_types();
    }

    public function render_header(string $pageTitle = '', ?ComplexWikiPage $complex_wiki_page = null): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle, $complex_wiki_page);

        $repository_viewer_action = $this->getRequest()->query->get(\Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION);

        switch ($repository_viewer_action)
        {
            case \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER :
                $title = 'BrowseAvailableWikiPages';
                break;
            case \Chamilo\Core\Repository\Viewer\Manager::ACTION_VIEWER :
                $title = 'PreviewWikiPage';
                break;
            default :
                $title = 'CreateWikiPage';
                break;
        }

        $html[] = '<div class="wiki-pane-content-title">' . Translation::get($title) . '</div>';
        $html[] = '<div class="wiki-pane-content-subtitle">' . Translation::get('In') . ' ' .
            $this->get_root_content_object()->get_title() . '</div>';

        return implode(PHP_EOL, $html);
    }
}
