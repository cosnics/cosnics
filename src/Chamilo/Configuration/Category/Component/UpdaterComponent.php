<?php
namespace Chamilo\Configuration\Category\Component;

use Chamilo\Configuration\Category\Form\CategoryForm;
use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.common.category_manager.component
 */
class UpdaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $category_id = $this->getRequest()->query->get(self::PARAM_CATEGORY_ID);

        if (!$this->get_parent()->allowed_to_edit_category($category_id))
        {
            throw new NotAllowedException();
        }

        $user = $this->get_user();

        $category_class_name = get_class($this->get_parent()->getCategory());
        $categories = $this->get_parent()->retrieve_categories(
            new EqualityCondition(
                new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_ID),
                new StaticConditionVariable($category_id)
            )
        );
        $category = $categories->current();

        $trail = $this->getBreadcrumbTrail();
        $this->set_parameter(self::PARAM_CATEGORY_ID, $this->getRequest()->query->get(self::PARAM_CATEGORY_ID));
        $trail->add(
            new Breadcrumb(
                $this->get_url(), Translation::get('UpdaterComponent', ['TITLE' => $category->get_name()])
            )
        );

        $form = new CategoryForm(
            CategoryForm::TYPE_EDIT, $this->get_url([self::PARAM_CATEGORY_ID => $category->get_id()]), $category, $user,
            $this
        );

        if ($form->validate())
        {
            $success = $form->update_category();

            $this->redirectWithMessage(
                Translation::get($success ? 'CategoryUpdated' : 'CategoryNotUpdated'), !$success, [
                    self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES,
                    self::PARAM_CATEGORY_ID => $category->get_parent()
                ]
            );
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
