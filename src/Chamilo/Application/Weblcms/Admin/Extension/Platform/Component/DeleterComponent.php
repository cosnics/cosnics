<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\StorageParameters;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Controller to create the schema
 */
class DeleterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (!$this->get_user()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $admin_ids = $this->getRequest()->query->get(self::PARAM_ADMIN_ID);

        try
        {
            if (empty($admin_ids))
            {
                $selected_entity_type = $this->get_selected_entity_type();
                $selected_entity_id = $this->get_selected_entity_id();

                if (empty($selected_entity_type) || empty($selected_entity_id))
                {
                    throw new NoObjectSelectedException(Translation::get('Target'));
                }
                else
                {

                    if (!is_array($selected_entity_id))
                    {
                        $selected_entity_id = [$selected_entity_id];
                    }

                    $conditions = [];
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ORIGIN),
                        new StaticConditionVariable(Admin::ORIGIN_INTERNAL)
                    );
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
                        new StaticConditionVariable($selected_entity_type)
                    );
                    $conditions[] = new InCondition(
                        new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID), $selected_entity_id
                    );
                    $condition = new AndCondition($conditions);

                    if (!DataManager::deletes(Admin::class, $condition))
                    {
                        $success = false;
                        $message = Translation::get(
                            'ObjectNotDeleted', ['OBJECT' => Translation::get('Target')], StringUtilities::LIBRARIES
                        );
                    }
                    else
                    {
                        // Let's determine where we want to redirect
                        // 1. Admin-instances with the same entity type
                        $condition = new EqualityCondition(
                            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
                            new StaticConditionVariable($selected_entity_type)
                        );

                        $condition = new AndCondition($condition);

                        $count = DataManager::count(Admin::class, new StorageParameters(condition: $condition));

                        if ($count > 0)
                        {
                            $parameters = [
                                self::PARAM_ACTION => self::ACTION_ENTITY,
                                self::PARAM_ENTITY_TYPE => $selected_entity_type
                            ];
                        }
                        else
                        {
                            $count = DataManager::count(Admin::class, new StorageParameters());

                            if ($count > 0)
                            {
                                $parameters = [self::PARAM_ACTION => self::ACTION_ENTITY];
                            }
                            else
                            {
                                $parameters = [self::PARAM_ACTION => self::ACTION_CREATE];
                            }
                        }
                    }
                }
            }
            else
            {

                if (!is_array($admin_ids))
                {
                    $admin_ids = [$admin_ids];
                }

                foreach ($admin_ids as $admin_id)
                {
                    $admin = DataManager::retrieve_by_id(Admin::class, $admin_id);

                    if ($admin->get_origin() == Admin::ORIGIN_EXTERNAL || !$admin->delete())
                    {
                        $success = false;
                        $message = Translation::get(
                            'ObjectNotDeleted', ['OBJECT' => Translation::get('Target')], StringUtilities::LIBRARIES
                        );
                        break;
                    }
                }

                // Let's try and determine where we want to redirect
                // 1. Admin-instances with the same entity id, entity type and target type
                $conditions = [];

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable($admin->get_entity_type())
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID),
                    new StaticConditionVariable($admin->get_entity_id())
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Admin::class, Admin::PROPERTY_TARGET_TYPE),
                    new StaticConditionVariable($admin->get_target_type())
                );

                $condition = new AndCondition($conditions);

                $count = DataManager::count(Admin::class, new StorageParameters(condition: $condition));

                if ($count > 0)
                {
                    $parameters = [
                        self::PARAM_ACTION => self::ACTION_TARGET,
                        self::PARAM_ENTITY_TYPE => $admin->get_entity_type(),
                        self::PARAM_ENTITY_ID => $admin->get_entity_id(),
                        self::PARAM_TARGET_TYPE => $admin->get_target_type()
                    ];
                }

                // 2. Admin-instance for the same entity type and entity id
                $conditions = [];

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable($admin->get_entity_type())
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID),
                    new StaticConditionVariable($admin->get_entity_id())
                );

                $condition = new AndCondition($conditions);

                $count = DataManager::count(Admin::class, new StorageParameters(condition: $condition));

                if ($count > 0)
                {
                    $parameters = [
                        self::PARAM_ACTION => self::ACTION_TARGET,
                        self::PARAM_ENTITY_TYPE => $admin->get_entity_type(),
                        self::PARAM_ENTITY_ID => $admin->get_entity_id()
                    ];
                }

                // 3. Admin-instances for the same entity type

                $condition = new EqualityCondition(
                    new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable($admin->get_entity_type())
                );

                $count = DataManager::count(Admin::class, new StorageParameters(condition: $condition));

                if ($count > 0)
                {
                    $parameters = [
                        self::PARAM_ACTION => self::ACTION_ENTITY,
                        self::PARAM_ENTITY_TYPE => $admin->get_entity_type()
                    ];
                }
                else
                {
                    $parameters = [self::PARAM_ACTION => self::ACTION_CREATE];
                }
            }
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $success = true;
        $message = Translation::get(
            'ObjectDeleted', ['OBJECT' => Translation::get('Target')], StringUtilities::LIBRARIES
        );

        $this->redirectWithMessage($message, !$success, $parameters);
    }
}