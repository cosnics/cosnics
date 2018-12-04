<?php
namespace Chamilo\Core\Repository\Quota\Rights\Form;

use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Repository\Quota\Rights\Rights;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataManager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

class RightsGroupForm extends \Chamilo\Libraries\Rights\Form\RightsForm
{
    const PROPERTY_ACCESS = 'access';
    const PROPERTY_TARGET_GROUPS = 'target_groups';

    protected function buildFormFooter()
    {
        $this->addElement(
            'category', $this->getTranslator()->trans('RightsGroupTargets', [], 'Chamilo\Core\Repository\Quota\Rights')
        );
        $this->addElement('html', '<div class="right">');

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(
            $this->getEntityByType(GroupEntityProvider::ENTITY_TYPE)->getEntityElementFinderType()
        );
        $this->addElement('advanced_element_finder', self::PROPERTY_TARGET_GROUPS, null, $types);

        $this->addElement('html', '</div></div>');
        $this->addElement('category');

        parent::buildFormFooter();
    }

    public function set_rights()
    {
        $values = $this->exportValues();

        $rights_util = Rights::getInstance();
        $location = $rights_util->get_quota_root();

        $targets_entities = Rights::getInstance()->get_quota_targets_entities();
        $location_id = $location->get_id();

        foreach ($values[self::PROPERTY_ACCESS] as $entity_type => $target_ids)
        {
            $to_add = array_diff($target_ids, (array) $targets_entities[$entity_type]);

            foreach ($to_add as $target_id)
            {
                if (!$rights_util->invert_quota_location_entity_right(
                    Rights::VIEW_RIGHT, $target_id, $entity_type, $location_id
                ))
                {
                    return false;
                }
            }

            foreach ($target_ids as $target_id)
            {
                $location_entity_right = Rights::getInstance()->get_quota_location_entity_right(
                    $target_id, $entity_type
                );

                foreach ($values[self::PROPERTY_TARGETS][PlatformGroupEntity::ENTITY_TYPE] as $group_id)
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            RightsLocationEntityRightGroup::class_name(),
                            RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
                        ), new StaticConditionVariable($location_entity_right->get_id())
                    );
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            RightsLocationEntityRightGroup::class_name(),
                            RightsLocationEntityRightGroup::PROPERTY_GROUP_ID
                        ), new StaticConditionVariable($group_id)
                    );
                    $condition = new AndCondition($conditions);

                    $existing_right_group = DataManager::retrieve(
                        RightsLocationEntityRightGroup::class_name(), new DataClassRetrieveParameters($condition)
                    );

                    if (!$existing_right_group instanceof RightsLocationEntityRightGroup)
                    {
                        $new_right_group = new RightsLocationEntityRightGroup();
                        $new_right_group->set_location_entity_right_id($location_entity_right->get_id());
                        $new_right_group->set_group_id($group_id);
                        if (!$new_right_group->create())
                        {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
}
