<?php
namespace Chamilo\Configuration\Category\Component;

use Chamilo\Configuration\Category\Form\ImpactViewForm;
use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Component to view the impact of a delete command
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImpactViewComponent extends Manager
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->supports_impact_view())
        {
            throw new \Exception(Translation :: get('ImpactViewNotSupported'));
        }

        $category_ids = $this->get_selected_category_ids();

        $category_class_name = get_class($this->get_parent()->get_category());
        $has_impact = $this->has_impact();
        $form = new ImpactViewForm($this->get_url(array(self::PARAM_CATEGORY_ID => $category_ids)));

        if ($form->validate() || ! $has_impact)
        {
            $failures = 0;

            foreach ($category_ids as $category_id)
            {
                if(!$this->get_parent()->allowed_to_delete_category($category_id))
                {
                    $failures ++;
                }

                $condition = new EqualityCondition(
                    new PropertyConditionVariable($category_class_name, PlatformCategory :: PROPERTY_ID),
                    new StaticConditionVariable($category_id)
                );
                $category = $this->get_parent()->retrieve_categories($condition)->next_result();

                if (is_null($category))
                {
                    $failures ++;
                    continue;
                }

                if (!$category->delete())
                {
                    $failures ++;
                }
            }

            $result = $this->get_result(
                $failures,
                count($category_ids),
                'CategoryNotDeleted',
                'CategoriesNotDeleted',
                'CategoryDeleted',
                'CategoryNotDeleted');

            $this->redirect(
                $result,
                $failures > 0,
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => null )
            );
        }
        else
        {
            $view = $this->render_impact_view();

            $html = array();

            $html[] = $this->render_header();
            $html[] = $view;
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders the impact view on the screen
     *
     * @return string
     */
    protected function render_impact_view()
    {
        $impact_view = $this->get_parent()->render_impact_view($this->get_selected_category_ids());

        if (is_null($impact_view))
        {
            $impact_view = '<div class="normal-message">' . Translation :: get('NoImpact') . '</div>';
        }

        return $impact_view;
    }

    protected function has_impact()
    {
        return $this->get_parent()->has_impact($this->get_selected_category_ids());
    }

    /**
     * Returns the selected category ids as an array
     *
     * @return string[]
     */
    protected function get_selected_category_ids()
    {
        $category_ids = $this->getRequest()->request->get(self :: PARAM_CATEGORY_ID);

        if(empty($category_ids)) {
            $category_ids = $this->getRequest()->query->get(self :: PARAM_CATEGORY_ID);
        }

        if (empty($category_ids))
        {
            throw new NoObjectSelectedException(Translation :: get('Category'));
        }

        $category_ids = (array) $category_ids;

        return $category_ids;
    }

    /**
     * Builds a result message with given parameters
     *
     * @param int $failures
     * @param int $count
     * @param string $fail_message_single
     * @param string $fail_message_multiple
     * @param string $succes_message_single
     * @param string $succes_message_multiple
     *
     * @return string
     */
    public function get_result($failures, $count, $fail_message_single, $fail_message_multiple, $succes_message_single,
        $succes_message_multiple)
    {
        if ($failures)
        {
            if ($count == 1)
            {
                $message = $fail_message_single;
            }
            else
            {
                $message = $fail_message_multiple;
            }
        }
        else
        {
            if ($count == 1)
            {
                $message = $succes_message_single;
            }
            else
            {
                $message = $succes_message_multiple;
            }
        }

        return Translation :: get($message);
    }
}
