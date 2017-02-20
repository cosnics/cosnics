<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.component
 */
/**
 * Description of reporting_template_viewerclass
 *
 * @author Sven Vanpoucke
 */
class RightsEditorComponent extends Manager
{
    const ADDITIONAL_INFORMATION_OBJECT_SEPARATOR = '  |  ';
    const LOCATION_TYPE_OBJECT = 'Objects';
    const LOCATION_TYPE_LOCATIONS = 'Locations';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $course = $this->get_course();
        if (! $course->is_course_admin($this->get_user()) && ! $this->get_user()->is_platform_admin())
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        $factory = new ApplicationFactory(
            \Chamilo\Core\Rights\Editor\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));

        $locations = $this->get_locations();
        if(empty($locations))
        {
            throw new UserException(Translation::getInstance()->getTranslation('NoRightsLocationsFound'));
        }

        $component = $factory->getComponent();
        $component->set_locations($locations);
        $component->set_entities($this->get_entities());
        $component->set_context('Chamilo\Application\Weblcms');
        return $component->run();
    }

    public function get_available_rights($location)
    {
        return $this->get_parent()->get_available_rights($location);
    }

    public function get_additional_information()
    {
        $publication_ids = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        if (! is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }

        if (count($publication_ids) > 0 && isset($publication_ids[0]))
        {
            $type = self::LOCATION_TYPE_OBJECT;
        }
        else
        {
            $type = self::LOCATION_TYPE_LOCATIONS;
        }

        $info = array();

        $translation_context = \Chamilo\Core\Rights\Manager::context();

        $info[] = Translation::get(
            "YouAreEditingRightsFor",
            array("TYPE" => Translation::get($type, null, $translation_context)),
            $translation_context);

        $info[] = '<br/>';

        switch ($type)
        {
            case self::LOCATION_TYPE_OBJECT :
                $publication_ids = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
                if (! is_array($publication_ids))
                {
                    $publication_ids = array($publication_ids);
                }

                foreach ($publication_ids as $publication_id)
                {
                    $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                        ContentObjectPublication::class_name(),
                        $publication_id);

                    if ($publication)
                    {
                        $content_object = $publication->get_content_object();
                        if ($content_object)
                        {
                            $info[] = '<a href="' . $this->get_publication_rights_editor_url($publication_id) . '">';
                            $info[] = $content_object->get_title();
                            $info[] = '</a>';
                            $info[] = self::ADDITIONAL_INFORMATION_OBJECT_SEPARATOR;
                        }
                    }
                }
                break;
            case self::LOCATION_TYPE_LOCATIONS :

                $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(
                    Course::class_name(),
                    Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE));

                if ($course)
                {
                    $info[] = '<a href="' . $this->get_course_rights_editor_url() . '">';
                    $info[] = $course->get_title();
                    $info[] = '</a>';
                    $info[] .= ' > ';
                }

                $selectedTool = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL);

                $context = 'Chamilo\Application\Weblcms\Tool\Implementation\\' . $selectedTool;
                $tool = Translation::getInstance()->getTranslation('TypeName', null, $context);

                if ($selectedTool && $selectedTool != 'Rights')
                {
                    $info[] = '<a href="' . $this->get_tool_rights_editor_url() . '">';
                    $info[] .= $tool;
                    $info[] = '</a>';
                    $info[] .= ' > ';

                    // categories
                    $category_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY);
                    if ($category_id)
                    {
                        // get the given category
                        $category = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                            ContentObjectPublicationCategory::class_name(),
                            $category_id);

                        if ($category)
                        {
                            // shift in the parents -> build categories in
                            // reverse order, from bottom to root
                            $index = count($info);
                            while ($category)
                            {
                                // construct the single link
                                $category_link = array();
                                $category_link[] = '<a href="' .
                                     $this->get_category_rights_editor_url($category->get_id()) . '">';
                                $category_link[] = $category->get_name();
                                $category_link[] = '</a>';
                                $category_link[] = ' > ';

                                // shift the link into the info array after
                                // document
                                array_splice($info, $index, 0, $category_link);

                                // parent
                                $category = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                                    ContentObjectPublicationCategory::class_name(),
                                    $category->get_parent());
                            }
                        }
                    }
                }
                break;
        }

        // remove last separator, or '<br/>' if no objects were found
        array_pop($info);

        return implode(PHP_EOL, $info);
    }

    public function add_category_string($category, $array)
    {
        if ($category)
        {
            $info[] = '<a href="' . $this->get_category_rights_editor_url($category->get_id()) . '">';
            $array[] .= $category->get_name();
            $info[] = '</a>';
            $array[] .= ' > ';
        }
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION,
            \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY);
    }

    public function get_entities()
    {
        $entities = array();

        $user_entity = new CourseUserEntity($this->get_course_id());

        $entities[CourseUserEntity::ENTITY_TYPE] = $user_entity;
        $entities[CourseGroupEntity::ENTITY_TYPE] = new CourseGroupEntity($this->get_course_id());

        $group_entity = new CoursePlatformGroupEntity($this->get_course_id());
        $entities[CoursePlatformGroupEntity::ENTITY_TYPE] = $group_entity;

        return $entities;
    }

    public function get_course_rights_editor_url()
    {
        return $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => \Chamilo\Application\Weblcms\Tool\Manager::class_to_type(
                    RightsTool),
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS,
                \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => null));
    }

    public function get_tool_rights_editor_url()
    {
        return $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS,
                \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => null));
    }

    public function get_category_rights_editor_url($category_id)
    {
        return $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS,
                \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => $category_id));
    }

    public function get_publication_rights_editor_url($publication_id)
    {
        return $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS,
                \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION => $publication_id));
    }
}
