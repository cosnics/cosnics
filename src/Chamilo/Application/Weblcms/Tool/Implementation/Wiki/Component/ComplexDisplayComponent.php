<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Manager;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\WikiDisplaySupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.wiki.component
 */
class ComplexDisplayComponent extends Manager implements DelegateComponent, WikiDisplaySupport
{

    private $publication;

    public function run()
    {
        $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $publication_id);
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            $this->redirect(
                Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        }

        $this->getCategoryBreadcrumbsGenerator()->generateBreadcrumbsForContentObjectPublication(
            BreadcrumbTrail::getInstance(), $this, $this->publication
        );

        $context = $this->publication->get_content_object()->package() . '\Display';

        return $this->getApplicationFactory()->getApplication(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    public function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    public function get_publication()
    {
        return $this->publication;
    }

    public function get_wiki_page_statistics_reporting_template_name()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\WikiPageTemplate::class_name();
    }

    public function get_wiki_statistics_reporting_template_name()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\WikiTemplate::class_name();
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS
    public function is_allowed_to_edit_content_object()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication) &&
             $this->publication->get_allow_collaboration();
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }
}
