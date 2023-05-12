<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Forms;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Tool\Form\PublicationSelectorForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This form can let the user select the sections and publications of a course for copying them to another course them.
 */
class CourseCopierForm extends FormValidator
{

    private $parent;

    private $publications;

    private $categories;

    private $courses;

    /**
     * Constructor
     *
     * @param string $parent
     * @param array $publications
     * @param array $categories
     * @param array $courses
     */
    public function __construct($parent, $publications, $categories, $courses)
    {
        parent::__construct('course_copier');
        $this->parent = $parent;
        $this->publications = $publications;
        $this->categories = $categories;
        $this->courses = $courses;
    }

    /**
     * Build the form for copying the course publications,sections and categorys
     */
    public function buildForm()
    {
        $defaults = [];
        $translations = [];

        $this->addElement('category', $this->getTranslation('Publications'));

        if (count($this->categories) > 0 || count($this->publications) > 0)
        {
            $this->addElement('html', '<div id="categories" style="display: none;">');

            foreach ($this->categories as $index => $category)
            {
                $tool = $category[ContentObjectPublicationCategory::PROPERTY_TOOL];
                $label = '';
                $id = 'categories[' . $category[ContentObjectPublicationCategory::PROPERTY_ID] . ']';

                $this->addElement('checkbox', $id, $label, $category[ContentObjectPublicationCategory::PROPERTY_NAME]);
                $defaults[$id] = true;
                if (!array_key_exists($tool, $translations))
                {
                    $translations[$tool] = $this->getTranslation(
                        'TypeName',
                        null,
                        \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($tool)
                    );
                }
            }

            $this->addElement('html', '</div><div id="publications" style="display: none;">');
            foreach ($this->publications as $publication)
            {
                $tool = $publication[ContentObjectPublication::PROPERTY_TOOL];
                $label = '';
                $id = 'publications[' . $publication[ContentObjectPublication::PROPERTY_ID] . ']';

                $this->addElement('checkbox', $id, $label, $publication[ContentObject::PROPERTY_TITLE]);
                $defaults[$id] = true;

                if (!array_key_exists($tool, $translations))
                {
                    $translations[$tool] = $this->getTranslation(
                        'TypeName',
                        null,
                        \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($tool)
                    );
                }
            }

            $this->addElement('html', '</div>');
            $publication_selector_form = new PublicationSelectorForm(
                $this->publications,
                $this->categories,
                $this->parent->get_course()->get_title(),
                true,
                $translations
            );
            $this->addElement('html', $publication_selector_form->render());

            $this->addElement('checkbox', 'content_object_categories', $this->getTranslation('PublicationCategories'));
            $defaults['content_object_categories'] = false;
        }

        $this->addElement('checkbox', 'course_groups', $this->getTranslation('CourseGroups'));
        $defaults['course_groups'] = false;

        $this->setDefaults($defaults);

        $this->addElement('category', $this->getTranslation('SelectCourse'));

        $current_code = $this->parent->get_course_id();
        $options = [];

        foreach ($this->courses as $course)
        {
            if ($course->get_id() != $current_code)
            {
                $options[$course->get_id()] = $course->get_title() . ' (' . $course->get_visual_code() . ')';
            }
        }

        asort($options);

        $this->addElement(
            'select',
            'course',
            $this->getTranslation('Course'),
            $options,
            array('multiple' => 'multiple', 'style' => 'min-height: 250px; max-height: 400px; min-width: 250px;')
        );
        $this->addRule('course', $this->getTranslation('Required', null, StringUtilities::LIBRARIES), 'required');

        $this->addElement('category', $this->getTranslation('CopyThisCourseInformation'));

        $this->addElement(
            'static', '', '', '<div class="alert alert-info">' . $this->getTranslation('ConfirmInformationMessage') . '</div>'
        );

        $this->addElement('checkbox', 'confirm', $this->getTranslation('Confirm', null, StringUtilities::LIBRARIES));
        $this->addRule(
            'confirm',
            $this->getTranslation('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );
        $prevnext = [];

        $prevnext[] = $this->createElement('style_submit_button', self::PARAM_SUBMIT, $this->getTranslation('Copy'));

        $this->addGroup($prevnext, 'buttons', '', ' ', false);
        $this->updateAttributes(array('action' => $this->parent->get_url()));
    }

    /**
     * Helper function to get translations
     *
     * @param string $variable
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = [], $context = null)
    {
        if (is_null($context))
        {
            $context = Manager::CONTEXT;
        }

        return Translation::getInstance()->getTranslation($variable, $parameters, $context);
    }
}
