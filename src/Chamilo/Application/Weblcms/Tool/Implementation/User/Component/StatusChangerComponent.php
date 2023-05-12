<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\UserStatusChange;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

abstract class StatusChangerComponent extends Manager
{

    protected $object;

    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $objects = $this->getRequest()->getFromRequestOrQuery(self::PARAM_OBJECTS);

        if (!$objects /* || !$status*/)
        {
            throw new NoObjectSelectedException(
                Translation::get('UserRelation')
            );
        }

        if (!is_array($objects))
        {
            $objects = array($objects);
        }

        $failed = 0;

        foreach ($objects as $this->object)
        {
            $relation = $this->get_relation(); // abstract -> implementation
            // gets user or group relation
            if (!$relation)
            {
                $failed ++;
            }

            // $relation->set_status($status);
            $relation->set_status($this->get_status());
            if (!$relation->update())
            {
                $failed ++;
            }

            $parameters[UserStatusChange::PROPERTY_USER_ID] = $this->get_user_id();
            $parameters[UserStatusChange::PROPERTY_SUBJECT_ID] = $this->object;
            $parameters[UserStatusChange::PROPERTY_NEW_STATUS] = $this->get_status();
            $parameters[UserStatusChange::PROPERTY_COURSE_ID] = Request::get(
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE
            );
            $parameters[UserStatusChange::PROPERTY_DATE] = time();
            Event::trigger('UserStatusChange', \Chamilo\Application\Weblcms\Manager::context(), $parameters);
        }

        $message = $this->get_general_result(
            $failed, count($objects), Translation::get('UserStatus'), Translation::get('UserStatusses'),
            Application::RESULT_TYPE_UPDATED
        );

        $this->redirectWithMessage(
            $message, $failed > 0, array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_UNSUBSCRIBE_BROWSER,
                self::PARAM_TAB => Request::get(self::PARAM_TAB)
            )
        );
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_UNSUBSCRIBE_BROWSER,
                        self::PARAM_TAB => Request::get(self::PARAM_TAB)
                    )
                ), Translation::get('UserToolUnsubscribeUserBrowserComponent')
            )
        );
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_TAB;
        if (Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP))
        {
            $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_GROUP;
        }

        return parent::getAdditionalParameters($additionalParameters);
    }

    abstract public function get_relation();

    abstract public function get_status();
}
