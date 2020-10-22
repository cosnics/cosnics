<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.component
 */
class CategoryMoverComponent extends Manager implements DelegateComponent
{

    private $tree;

    private $level = 1;

    public function run()
    {
        if ($this->is_allowed(WeblcmsRights::ADD_RIGHT))
        {
            $form = $this->build_move_to_category_form();

            if (!$form)
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $this->display_error_message('CategoryFormCouldNotBeBuild');
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }

            $publication_ids =
                $this->getRequest()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

            if (!is_array($publication_ids))
            {
                $publication_ids = array($publication_ids);
            }

            $form->addElement('hidden', 'pids', implode('-', $publication_ids));

            if ($form->validate())
            {
                $values = $form->exportValues();
                $publication_ids = explode('-', $values['pids']);
                // TODO: update all publications in a single action/query

                foreach ($publication_ids as $publication_id)
                {
                    $publication = DataManager::retrieve_by_id(
                        ContentObjectPublication::class, $publication_id
                    );
                    $publication->set_category_id($form->exportValue('category'));
                    $publication->update();

                    if ($publication->get_category_id())
                    {
                        $new_parent_id =
                            WeblcmsRights::getInstance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                                WeblcmsRights::TYPE_COURSE_CATEGORY, $publication->get_category_id(),
                                $publication->get_course_id()
                            );
                    }
                    else
                    {
                        $condition = new EqualityCondition(
                            new PropertyConditionVariable(CourseTool::class, CourseTool::PROPERTY_NAME),
                            new StaticConditionVariable($publication->get_tool())
                        );
                        $course_modules = DataManager::retrieves(
                            CourseTool::class, new DataClassRetrievesParameters($condition)
                        );

                        $course_module_id = $course_modules[0]->get_id();
                        $new_parent_id =
                            WeblcmsRights::getInstance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                                WeblcmsRights::TYPE_COURSE_MODULE, $course_module_id, $publication->get_course_id()
                            );
                    }

                    $location = WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
                        WeblcmsRights::TYPE_PUBLICATION, $publication->get_id(), $publication->get_course_id()
                    );

                    if ($location)
                    {
                        $location->move($new_parent_id);
                    }
                }
                if (count($publication_ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectMoved', array('OBJECT' => Translation::get('Publication')), Utilities::COMMON_LIBRARIES
                    );
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsMoved', array('OBJECTS' => Translation::get('Publications')),
                        Utilities::COMMON_LIBRARIES
                    );
                }
                $this->redirect(
                    $message, false, array(
                        'tool_action' => null,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => null
                    )
                );
            }
            else
            {
                // $message = $form->toHtml();
                $trail = BreadcrumbTrail::getInstance();

                if (count($publication_ids) > 1)
                {
                    $trail->add(new Breadcrumb($this->get_url(), Translation::get('PublicationsMover')));
                }
                else
                {
                    $publication = DataManager::retrieve_by_id(
                        ContentObjectPublication::class, $publication_ids[0]
                    );

                    $trail->add(
                        new Breadcrumb(
                            $this->get_url(), Translation::get(
                            'PublicationMover', array('PUBLICATION' => $publication->get_content_object()->get_title())
                        )
                        )
                    );
                }

                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    public function build_category_tree($parent_id, $exclude, $is_course_admin)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
            ), new StaticConditionVariable($parent_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->get_course_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->get_tool_id())
        );

        $condition = new AndCondition($conditions);

        $categories = DataManager::retrieves(
            ContentObjectPublicationCategory::class, new DataClassRetrievesParameters($condition)
        );

        while ($cat = $categories->next_result())
        {
            if ($is_course_admin || WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
                    WeblcmsRights::ADD_RIGHT, $cat->get_id(), WeblcmsRights::TYPE_COURSE_CATEGORY,
                    $this->get_course_id()
                ))
            {
                // if ($cat->get_id() != $exclude)
                // {
                $this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
                // }
                $this->level ++;
                $this->build_category_tree($cat->get_id(), $exclude, $is_course_admin);
                $this->level --;
            }
        }
    }

    public function build_move_to_category_form()
    {
        $publication_ids = $this->getRequest()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        if (!is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }
        if (count($publication_ids) > 0)
        {
            $pub = DataManager::retrieve_by_id(
                ContentObjectPublication::class, $publication_ids[0]
            );
            if ($pub)
            {
                $cat = $pub->get_category_id();
                $course = $this->get_course();
                $is_course_admin = $course->is_course_admin($this->get_user());

                if ($cat != 0)
                {
                    $module = DataManager::retrieve_course_tool_by_name(
                        $this->get_tool_id()
                    );

                    if ($is_course_admin || WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
                            WeblcmsRights::ADD_RIGHT, $module->get_id(), WeblcmsRights::TYPE_COURSE_MODULE,
                            $course->get_id()
                        ))
                    {
                        $this->tree[0] = Translation::get('Root');
                    }
                }
                $this->build_category_tree(0, $cat, $is_course_admin);

                if (count($this->tree) < 1)
                {
                    $this->redirect(
                        Translation::get('NoCategoriesAvailable'), true, null,
                        array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION)
                    );
                }

                $form = new FormValidator(
                    'select_category', FormValidator::FORM_METHOD_POST, $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_ids
                    )
                )
                );
                foreach ($publication_ids as $publication_id)
                {
                    $publications[] = DataManager::retrieve_by_id(
                        ContentObjectPublication::class, $publication_id
                    )->get_content_object()->get_title();
                }
                $form->addElement(
                    'static', null, Translation::get(
                    'ObjectSelected', array(
                        'OBJECT' => Translation::get(count($publication_ids) > 1 ? 'Publications' : 'Publication')
                    ), Utilities::COMMON_LIBRARIES
                ), implode('<br>', $publications)
                );

                $select = $form->addElement(
                    'select', 'category', Translation::get('Category', null, Utilities::COMMON_LIBRARIES)/*,
                    $this->tree*/
                );

                foreach ($this->tree as $key => $value)
                {
                    if ($cat == $key)
                    {
                        $select->addOption($value, $key, array('disabled'));
                    }
                    else
                    {
                        $select->addOption($value, $key);
                    }
                }

                // $form->addElement('submit', 'submit', Translation ::
                // get('Ok', null ,Utilities:: COMMON_LIBRARIES));
                $buttons[] = $form->createElement(
                    'style_submit_button', 'submit', Translation::get('Move', null, Utilities::COMMON_LIBRARIES), null,
                    null, new FontAwesomeGlyph('move')
                );
                $buttons[] = $form->createElement(
                    'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
                );

                $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

                return $form;
            }
        }
    }
}
