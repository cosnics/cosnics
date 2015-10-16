<?php
namespace Chamilo\Core\Lynx\Remote\Component;

use Chamilo\Core\Lynx\Remote\DataClass\Package;
use Chamilo\Core\Lynx\Remote\DataManager;
use Chamilo\Core\Lynx\Remote\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class SynchronizerComponent extends Manager
{

    public function run()
    {
        $parameters = new DataClassRetrievesParameters(
            new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Lynx\Source\DataClass\Source :: class_name(),
                    \Chamilo\Core\Lynx\Source\DataClass\Source :: PROPERTY_STATUS),
                new StaticConditionVariable(\Chamilo\Core\Lynx\Source\DataClass\Source :: STATUS_ACTIVE)));
        $sources = \Chamilo\Core\Lynx\Source\DataManager :: retrieves(
            \Chamilo\Core\Lynx\Source\DataClass\Source :: class_name(),
            $parameters);

        $failures = 0;

        while ($source = $sources->next_result())
        {

            // Retrieve the source package list
            $packages = Package :: collection($source);

            foreach ($packages as $package)
            {
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Package :: class_name(), Package :: PROPERTY_SOURCE_ID),
                    new StaticConditionVariable($source->get_id()));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Package :: class_name(), Package :: PROPERTY_CONTEXT),
                    new StaticConditionVariable($package->get_context()));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Package :: class_name(), Package :: PROPERTY_VERSION),
                    new StaticConditionVariable($package->get_version()));
                $condition = new AndCondition($conditions);

                $parameters = new DataClassRetrieveParameters($condition);
                $existing_object = DataManager :: retrieve(Package :: class_name(), $parameters);

                if ($existing_object instanceof Package)
                {
                    $package->set_id($existing_object->get_id());
                    try
                    {
                        $package->update();
                    }
                    catch (\Exception $e)
                    {
                        $failures ++;
                    }
                }
                else
                {
                    if (! $package->create())
                    {
                        $failures ++;
                    }
                }
            }
        }

        $this->redirect(
            Translation :: get($failures > 0 ? 'SomeSynchronizationsFailed' : 'SynchronizationFinished'),
            $failures > 0,
            array(
                \Chamilo\Core\Lynx\Manager :: PARAM_ACTION => \Chamilo\Core\Lynx\Manager :: ACTION_REMOTE,
                self :: PARAM_ACTION => null));
    }
}
