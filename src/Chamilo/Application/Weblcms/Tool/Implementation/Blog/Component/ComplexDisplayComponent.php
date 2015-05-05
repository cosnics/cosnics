<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Blog\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Blog\Manager;
use Chamilo\Core\Repository\ContentObject\Blog\Display\BlogDisplaySupport;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: blog_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.blog.component
 */

/**
 * Represents the view component for the assessment tool.
 */
class ComplexDisplayComponent extends Manager implements DelegateComponent, BlogDisplaySupport
{

    private $publication;

    public function run()
    {
        $publication_id = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $publication_id);

        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $this->publication))
        {
            $this->redirect(
                Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID));
        }
        BreadcrumbTrail :: get_instance()->add(new Breadcrumb(null, $this->get_root_content_object()->get_title()));

        $context = $this->publication->get_content_object()->package() . '\Display';
        $factory = new ApplicationFactory($this->getRequest(), $context, $this->get_user(), $this);
        return $factory->run();
    }

    public function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS
    public function is_allowed_to_edit_content_object()
    {
        return $this->publication->get_content_object()->has_right(
            RepositoryRights :: COLLABORATE_RIGHT,
            $this->get_user_id());
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_add_child()
    {
        return $this->publication->get_content_object()->has_right(
            RepositoryRights :: COLLABORATE_RIGHT,
            $this->get_user_id());
    }

    public function is_allowed_to_delete_child()
    {
        return $this->publication->get_content_object()->has_right(
            RepositoryRights :: COLLABORATE_RIGHT,
            $this->get_user_id());
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }
}
