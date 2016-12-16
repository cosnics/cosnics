<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Admin\Integration\Chamilo\Core\Tracking\Storage\DataClass\Online;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Table\WhoisOnline\WhoisOnlineTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: whois_online.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 *
 * @package admin.lib.admin_manager.component
 */

/**
 * Component to view whois online
 */
class WhoisOnlineComponent extends Manager implements TableSupport
{

    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ViewWhoisOnline');

        $world = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'whoisonlineaccess'));

        if ($world == "1" || ($this->get_user_id() && $world == "2"))
        {
            $user_id = Request::get('uid');
            if (isset($user_id))
            {
                $output = $this->get_user_html($user_id);
            }
            else
            {
                $currentUrl = $this->get_url();

                try
                {
                    $output = $this->get_table_html();
                }
                catch (\Exception $ex)
                {
                    if ($ex->getMessage() == 'Invalid page number')
                    {
                        $output = '<div class="alert alert-warning">' . Translation::getInstance()->getTranslation(
                                'WhoisOnlineTableChanged', array('URL' => $currentUrl), Manager::context()
                            ) . '</div>';
                    }
                }
            }

            $html = array();

            $html[] = $this->render_header();
            $html[] = $output;
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    private function get_table_html()
    {
        $parameters = $this->get_parameters(true);

        $table = new WhoisOnlineTable($this);

        $html = array();
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($class_name)
    {
        $pastTime = strtotime(
            '-' . Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'timelimit')) . ' seconds',
            time()
        );

        $parameters = new DataClassDistinctParameters(
            new InequalityCondition(
                new PropertyConditionVariable(Online::class_name(), Online::PROPERTY_LAST_ACCESS_DATE),
                InEqualityCondition::GREATER_THAN,
                new StaticConditionVariable($pastTime)
            ),
            Online::PROPERTY_USER_ID
        );

        $userIds = DataManager::distinct(Online::class_name(), $parameters);

        if (!empty($userIds))
        {
            return new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $userIds);
        }
        else
        {
            return new EqualityCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                new StaticConditionVariable(- 1)
            );
        }
    }

    private function get_user_html($user_id)
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            (int) $user_id
        );

        $html[] = '<br /><div style="float: left; width: 150px;">';
        $html[] = Translation::get('Username', array(), \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = Translation::get('Fullname', array(), \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = Translation::get('OfficialCode', array(), \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = Translation::get('Email', array(), \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = Translation::get('Status', array(), \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = '</div><div style="float: left; width: 250px;">';
        $html[] = $user->get_username() . '<br />';
        $html[] = $user->get_fullname() . '<br />';
        $html[] = $user->get_official_code() . '<br />';
        $html[] = $user->get_email() . '<br />';
        $html[] = $user->get_status_name() . '<br />';
        $html[] = '</div><div style="float: right; max-width: 400px;">';

        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $user_id
            )
        );

        $html[] = '<img src="' . $profilePhotoUrl->getUrl() . '" />';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('admin_whois_online');
    }
}
