<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Admin\Integration\Chamilo\Core\Tracking\Storage\DataClass\Online;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Table\WhoIsOnlineTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
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

        if ($this->getUser() instanceof User)
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
                catch (Exception $ex)
                {
                    if ($ex->getMessage() == 'Invalid page number')
                    {
                        $output = '<div class="alert alert-warning">' . Translation::getInstance()->getTranslation(
                                'WhoisOnlineTableChanged', ['URL' => $currentUrl], Manager::context()
                            ) . '</div>';
                    }
                }
            }

            $html = [];

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

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getWhoIsOnlineTableCondition()
    {
        $pastTime = strtotime(
            '-' . Configuration::getInstance()->get_setting(['Chamilo\Core\Admin', 'timelimit']) . ' seconds', time()
        );

        $parameters = new DataClassDistinctParameters(
            new ComparisonCondition(
                new PropertyConditionVariable(Online::class, Online::PROPERTY_LAST_ACCESS_DATE),
                ComparisonCondition::GREATER_THAN, new StaticConditionVariable($pastTime)
            ), new RetrieveProperties([new PropertyConditionVariable(Online::class, Online::PROPERTY_USER_ID)])
        );

        $userIds = DataManager::distinct(Online::class, $parameters);

        if (!empty($userIds))
        {
            return new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $userIds);
        }
        else
        {
            return new EqualityCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_ID), new StaticConditionVariable(- 1)
            );
        }
    }

    public function getWhoIsOnlineTableRenderer(): WhoIsOnlineTableRenderer
    {
        return $this->getService(WhoIsOnlineTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    private function get_table_html()
    {
        $totalNumberOfItems = $this->getUserService()->countUsers($this->getWhoIsOnlineTableCondition());
        $whoIsOnlineTableRenderer = $this->getWhoIsOnlineTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $whoIsOnlineTableRenderer->getParameterNames(), $whoIsOnlineTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getWhoIsOnlineTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $whoIsOnlineTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $whoIsOnlineTableRenderer->render($tableParameterValues, $users);
    }

    private function get_user_html($user_id)
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class, (int) $user_id
        );

        $html[] = '<br /><div style="float: left; width: 150px;">';
        $html[] = Translation::get('Username', [], \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = Translation::get('Fullname', [], \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = Translation::get('OfficialCode', [], \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = Translation::get('Email', [], \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = Translation::get('Status', [], \Chamilo\Core\User\Manager::context()) . ':<br />';
        $html[] = '</div><div style="float: left; width: 250px;">';
        $html[] = $user->get_username() . '<br />';
        $html[] = $user->get_fullname() . '<br />';
        $html[] = $user->get_official_code() . '<br />';
        $html[] = $user->get_email() . '<br />';
        $html[] = $user->get_status_name() . '<br />';
        $html[] = '</div><div style="float: right; max-width: 400px;">';

        $profilePhotoUrl = new Redirect(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $user_id
            ]
        );

        $html[] = '<img src="' . $profilePhotoUrl->getUrl() . '" />';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
