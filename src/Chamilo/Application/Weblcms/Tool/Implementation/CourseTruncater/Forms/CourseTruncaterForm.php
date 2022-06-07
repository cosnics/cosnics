<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Forms;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Form\PublicationSelectorForm;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This form can let the user select the sections and publications of a course for deleting them.
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class CourseTruncaterForm extends FormValidator
{

    private $parent;

    private $publications;

    private $categories;

    private $course_sections;

    /**
     * Constructor
     * 
     * @param object $parent
     * @param array $publications
     * @param array $categories
     * @param array $course_sections
     */
    public function __construct($parent, $publications, $categories, $course_sections)
    {
        parent::__construct('course_truncater');
        
        $this->parent = $parent;
        $this->publications = $publications;
        $this->categories = $categories;
        $this->course_sections = $course_sections;
    }

    /**
     * Builds the form
     */
    public function buildForm()
    {
        $defaults = [];
        $translations = [];
        
        $this->addElement('category', Translation::get('Publications'));
        
        $this->addElement('html', '<div id="categories" style="display: none;">');
        foreach ($this->categories as $category)
        {
            $tool = $category[ContentObjectPublicationCategory::PROPERTY_TOOL];
            $label = '';
            $id = 'categories[' . $category[ContentObjectPublicationCategory::PROPERTY_ID] . ']';
            
            $this->addElement('checkbox', $id, $label, $category[ContentObjectPublicationCategory::PROPERTY_NAME]);
            $defaults[$id] = true;
            if (! array_key_exists($tool, $translations))
            {
                $translations[$tool] = Translation::get(
                    'TypeName', 
                    null, 
                    Manager::get_tool_type_namespace($tool));
            }
        }
        
        $this->addElement('html', '</div><div id="publications" style="display: none;">');
        foreach ($this->publications as $publication)
        {
            $tool = $publication[ContentObjectPublication::PROPERTY_TOOL];
            $label = "";
            $id = 'publications[' . $publication[ContentObjectPublication::PROPERTY_ID] . ']';
            
            $this->addElement('checkbox', $id, $label, $publication[ContentObject::PROPERTY_TITLE]);
            $defaults[$id] = true;
            if (! array_key_exists($tool, $translations))
            {
                $translations[$tool] = Translation::get(
                    'TypeName', 
                    null, 
                    Manager::get_tool_type_namespace($tool));
            }
        }
        
        // $this->addFormRule(array('PublicationSelectionMaintenanceWizardPage', 'count_selected_publications'));
        
        $this->addElement('html', '</div>');
        $publication_selector_form = new PublicationSelectorForm(
            $this->publications, 
            $this->categories, 
            $this->parent->get_course()->get_title(), 
            true, 
            $translations);
        $this->addElement('html', $publication_selector_form->render());
        $this->addElement('checkbox', 'content_object_categories', Translation::get('PublicationCategories'));
        
        if (count($this->course_sections) > 0)
        {
            $this->addElement('html', '<h3>' . Translation::get('CourseSections') . '</h3>');
            foreach ($this->course_sections as $course_section)
            {
                $id = 'course_sections[' . $course_section[CourseSection::PROPERTY_ID] . ']';
                $this->addElement('checkbox', $id, $course_section[CourseSection::PROPERTY_NAME]);
                $defaults[$id] = true;
            }
        }
        
        $defaults['content_object_categories'] = true;
        
        $this->setDefaults($defaults);
        
        $this->addElement('category', Translation::get('EmptyThisCourseInformation'));
        $this->addElement('checkbox', 'confirm', Translation::get('Confirm', null, StringUtilities::LIBRARIES));
        $this->addRule(
            'confirm', 
            Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required');
        $prevnext = [];
        $prevnext[] = $this->createElement('style_submit_button', self::PARAM_SUBMIT, Translation::get('Truncate'));
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->updateAttributes(array('action' => $this->parent->get_url()));
    }
}
