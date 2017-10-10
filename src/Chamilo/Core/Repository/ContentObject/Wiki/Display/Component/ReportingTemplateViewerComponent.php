<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ReportingTemplateViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case self::ACTION_PAGE_STATISTICS :
                $template = $this->get_parent()->get_wiki_page_statistics_reporting_template_name();
                break;
            case self::ACTION_STATISTICS :
                $template = $this->get_parent()->get_wiki_statistics_reporting_template_name();
                break;
            case self::ACTION_ACCESS_DETAILS :
                $template = 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\PublicationDetailTemplate';
                break;
        }

        $component = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Reporting\Viewer\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $component->set_template_by_name($template);
        return $component->run();
    }

    public function render_header()
    {
        if ($this->get_action() == self::ACTION_STATISTICS)
        {
            return parent::render_header();
        }
        else
        {
            $complex_wiki_page_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
            $complex_wiki_page = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexContentObjectItem::class_name(),
                $complex_wiki_page_id);
            $wiki_page = $complex_wiki_page->get_ref_object();

            $html = array();

            $html[] = parent::render_header($complex_wiki_page);

            $html[] = '<div class="wiki-pane-content-title">' . Translation::get('Statistics') . ' ' .
                 $wiki_page->get_title() . '</div>';
            $html[] = '<div class="wiki-pane-content-subtitle">' . Translation::get(
                'From',
                null,
                Utilities::COMMON_LIBRARIES) . ' ' . $this->get_root_content_object()->get_title() . '</div>';
            return implode(PHP_EOL, $html);
        }
    }
}
