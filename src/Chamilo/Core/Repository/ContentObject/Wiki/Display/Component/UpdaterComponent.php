<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdaterComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Display\Action\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }

    public function render_header()
    {
        $complex_wiki_page_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $complex_wiki_page = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class_name(), $complex_wiki_page_id
        );
        $wiki_page = $complex_wiki_page->get_ref_object();

        $html = array();

        $html[] = parent::render_header($complex_wiki_page);
        $html[] = '<h3 id="page-title">' . Translation::get('Edit', null, Utilities::COMMON_LIBRARIES) . ' ' .
            $wiki_page->get_title() . '</h3>';

        return implode(PHP_EOL, $html);
    }
}
