<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

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
            \Chamilo\Core\Repository\Display\Action\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }

    public function render_header(string $pageTitle = '', ?ComplexWikiPage $complex_wiki_page = null): string
    {
        $complex_wiki_page_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $complex_wiki_page = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class, $complex_wiki_page_id
        );
        $wiki_page = $complex_wiki_page->get_ref_object();

        $html = [];

        $html[] = parent::render_header($pageTitle, $complex_wiki_page);
        $html[] = '<h3 id="page-title">' . Translation::get('Edit', null, StringUtilities::LIBRARIES) . ' ' .
            $wiki_page->get_title() . '</h3>';

        return implode(PHP_EOL, $html);
    }
}
