<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Forms\CourseTruncaterForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Service\PublicationSelectorDataMapper;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/*
 * Component for emptying the course publication,publication categories and sections @author Maarten Volckaert -
 * Hogeschool Gent @author Mattias De Pauw - Hogeschool Gent
 */
class BrowserComponent extends Manager
{

    protected $course_truncater_form;

    /**
     * checks whether the user has the rights if so create empty form if
     * validate delete selected publications section.
     */
    public function run()
    {
        $course_id = $this->get_course_id();
        
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new NotAllowedException();
        }
        
        $count_custom_course_sections = DataManager::count_custom_course_sections_from_course(
            $course_id);
        
        if (\Chamilo\Application\Weblcms\Course\Storage\DataManager::count_course_content_object_publications(
            $course_id) == 0 && $count_custom_course_sections == 0)
        {
            throw new UserException(Translation::get('NoPublications'));
        }
        
        $publicationSelectorDataMapper = new PublicationSelectorDataMapper();
        
        $publications = $publicationSelectorDataMapper->getContentObjectPublicationsForPublicationSelector($course_id);
        $categories = $publicationSelectorDataMapper->getContentObjectPublicationCategoriesForPublicationSelector(
            $course_id);
        
        if ($count_custom_course_sections > 0)
        {
            $course_sections = DataManager::retrieve_custom_course_sections_as_array(
                $course_id);
        }
        else
        {
            $course_sections = [];
        }
        
        $this->course_truncater_form = new CourseTruncaterForm($this, $publications, $categories, $course_sections);
        $this->course_truncater_form->buildForm();
        
        if ($this->course_truncater_form->validate())
        {
            $values = $this->course_truncater_form->exportValues();
            
            if (isset($values['publications']) || isset($values["course_sections"]) ||
                 $values['content_object_categories'] == 1)
            {
                $publications_ids = array_keys($values['publications']);
                $delete_categories = $values['content_object_categories'];
                $categories_ids = array_keys($values['categories']);
                $course_sections_ids = array_keys($values['course_sections']);
                
                $success = true;
                if (count($course_sections_ids) > 0)
                {
                    $success = $success && DataManager::delete_course_sections(
                        $course_sections_ids);
                }
                if ($delete_categories == 1 && count($categories_ids) > 0)
                {
                    $success = $success && DataManager::delete_publications_and_categories(
                        $publications_ids, 
                        $categories_ids);
                }
                else
                {
                    $success = $success && DataManager::delete_publications(
                        $publications_ids);
                }
                
                if ($success)
                {
                    $this->redirectWithMessage(
                        Translation::get('AllSelectedObjectsRemoved'), 
                        false, 
                        array(
                            \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_WEBLCMS_HOME));
                }
                else
                {
                    throw new Exception(Translation::get('NotAllSelectedObjectsRemoved'));
                }
            }
            else
            {
                $html = [];
                
                $html[] = $this->render_header();
                $html[] = Display::error_message(Translation::get('SelectAItem'));
                $html[] = $this->course_truncater_form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = [];
            
            $html[] = $this->render_header();
            $html[] = $this->course_truncater_form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
