<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\WikiPageTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\WikiTemplate;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Manager;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\WikiDisplaySupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package application.lib.weblcms.tool.wiki.component
 */
class ComplexDisplayComponent extends Manager implements DelegateComponent, WikiDisplaySupport
{

    private $publication;

    public function run()
    {
        $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $publication_id
        );
        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            $this->redirectWithMessage(
                Translation::get('NotAllowed', null, StringUtilities::LIBRARIES), true, [], [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                ]
            );
        }

        $this->getCategoryBreadcrumbsGenerator()->generateBreadcrumbsForContentObjectPublication(
            BreadcrumbTrail::getInstance(), $this, $this->publication
        );

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    public function get_publication()
    {
        return $this->publication;
    }

    public function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    public function get_wiki_page_statistics_reporting_template_name()
    {
        return WikiPageTemplate::class;
    }

    public function get_wiki_statistics_reporting_template_name()
    {
        return WikiTemplate::class;
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_feedback($feedback)
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node)
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication) &&
            $this->publication->get_allow_collaboration();
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node)
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }
}
