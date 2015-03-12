<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Core\User\Form\AccountForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package Chamilo\Core\User\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountComponent extends Manager implements NoContextComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        BreadcrumbTrail :: get_instance()->remove(1);

        // not allowed for anonymous user
        if ($this->get_user()->is_anonymous_user())
        {
            throw new NotAllowedException();
        }

        Page :: getInstance()->setSection(self :: SECTION_MY_ACCOUNT);

        $user = $this->get_user();

        $form = new AccountForm(AccountForm :: TYPE_EDIT, $user, $this->get_url());

        if ($form->validate())
        {
            $success = $form->update_account();
            if (! $success)
            {
                if (isset($_FILES['picture_uri']) && $_FILES['picture_uri']['error'])
                {
                    $neg_message = 'FileTooBig';
                }
                else
                {
                    $neg_message = 'UserProfileNotUpdated';
                }
            }
            else
            {
                $neg_message = 'UserProfileNotUpdated';
                $pos_message = 'UserProfileUpdated';
            }
            $this->redirect(
                Translation :: get($success ? $pos_message : $neg_message),
                ($success ? false : true),
                array(Application :: PARAM_ACTION => self :: ACTION_VIEW_ACCOUNT));
        }
        else
        {
            $actions = array();

            $actions[] = self :: ACTION_VIEW_ACCOUNT;

            $actions[] = self :: ACTION_USER_SETTINGS;

            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_APPLICATION),
                new StaticConditionVariable(self :: context()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_NAME),
                new StaticConditionVariable('account_fields'));
            $condition = new AndCondition($conditions);

            $extra_form = \Chamilo\Configuration\Form\Storage\DataManager :: retrieve(
                Instance :: class_name(),
                new DataClassRetrieveParameters($condition));

            if ($extra_form instanceof \Chamilo\Configuration\Form\Storage\DataClass\Instance &&
                 count($extra_form->get_elements()) > 0)
            {
                $actions[] = self :: ACTION_ADDITIONAL_ACCOUNT_INFORMATION;
            }

            $html = array();

            $html[] = $this->render_header();

            if (count($actions) > 1)
            {
                $tabs = new DynamicVisualTabsRenderer('account', $form->toHtml());
                foreach ($actions as $action)
                {
                    $selected = ($action == 'account' ? true : false);

                    $label = htmlentities(
                        Translation :: get(
                            (string) StringUtilities :: getInstance()->createString($action)->upperCamelize() . 'Title'));
                    $link = $this->get_url(array(self :: PARAM_ACTION => $action));

                    $tabs->add_tab(
                        new DynamicVisualTab(
                            $action,
                            $label,
                            Theme :: getInstance()->getImagePath('Chamilo\Core\User', 'Place/' . $action),
                            $link,
                            $selected));
                }
                $html[] = $tabs->render();
            }
            else
            {
                $html[] = $form->toHtml();
            }

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_account');
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
