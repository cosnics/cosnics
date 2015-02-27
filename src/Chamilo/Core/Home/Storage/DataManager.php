<?php
namespace Chamilo\Core\Home\Storage;

use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\BlockConfiguration;
use Chamilo\Core\Home\Storage\DataClass\BlockRegistration;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Row;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: home_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * 
 * @package home.lib This is a skeleton for a data manager for the Home application.
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'home_';

    public static function determine_user_id()
    {
        $current_user_id = \Chamilo\Libraries\Platform\Session\Session :: get_user_id();
        $current_user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            User :: class_name(), 
            intval($current_user_id));
        
        $user_home_allowed = PlatformSetting :: get('allow_user_home', Manager :: context());
        $general_mode = \Chamilo\Libraries\Platform\Session\Session :: retrieve(__NAMESPACE__ . '\general');
        
        if ($current_user instanceof User)
        {
            if ($general_mode && $current_user->is_platform_admin())
            {
                return 0;
            }
            elseif ($user_home_allowed)
            {
                return $current_user->get_id();
            }
            elseif (! $user_home_allowed && $current_user->is_platform_admin())
            {
                return 0;
            }
            else
            {
                return false;
            }
        }
    }

    public static function get_platform_blocks()
    {
        $blocks = array();
        
        $registrations = self :: retrieves(BlockRegistration :: class_name());
        
        while ($registration = $registrations->next_result())
        {
            $context = $registration->get_context();
            $block = $registration->get_block();
            
            $home_block = new Block();
            $home_block->set_registration_id($registration->get_id());
            
            // $renderer = HomeRenderer :: factory(HomeRenderer :: TYPE_BASIC);
            // $block_object = Block :: factory($renderer, $home_block);
            
            // if (! $block_object->is_deletable())
            // {
            // continue;
            // }
            
            $parent = ClassnameUtilities :: getInstance()->getNamespaceParent(
                ClassnameUtilities :: getInstance()->getNamespaceParent(
                    ClassnameUtilities :: getInstance()->getNamespaceParent($context)));
            
            $blocks[$context]['name'] = Translation :: get('TypeName', null, $parent);
            $blocks[$context]['image'] = Theme :: getInstance()->getImagesPath($parent) . 'Logo/16.png';
            $blocks[$context]['components'][] = array(
                BlockRendition :: BLOCK_PROPERTY_ID => $block, 
                BlockRendition :: BLOCK_PROPERTY_NAME => Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($block)->upperCamelize(), 
                    null, 
                    $context), 
                BlockRendition :: BLOCK_PROPERTY_IMAGE => BlockRendition :: get_image_path($context, $block));
        }
        
        return $blocks;
    }

    /**
     *
     * @param $context string
     * @param $block string
     * @return Ambigous <BlockRegistration, null>
     */
    public static function retrieve_home_block_registration_by_context_and_block($context, $block)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(BlockRegistration :: class_name(), BlockRegistration :: PROPERTY_CONTEXT), 
            new StaticConditionVariable($context));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(BlockRegistration :: class_name(), BlockRegistration :: PROPERTY_BLOCK), 
            new StaticConditionVariable($block));
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(BlockRegistration :: class_name(), new DataClassRetrieveParameters($condition));
    }

    public static function retrieve_home_tab_blocks($home_tab)
    {
        $row_condition = new EqualityCondition(
            new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_TAB), 
            new StaticConditionVariable($home_tab->get_id()));
        $column_condition = new SubselectCondition(
            new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_ROW), 
            new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_ID), 
            Row :: get_table_name(), 
            $row_condition);
        $condition = new SubselectCondition(
            new PropertyConditionVariable(Block :: class_name(), Block :: PROPERTY_COLUMN), 
            new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_ID), 
            Column :: get_table_name(), 
            $column_condition);
        
        return self :: retrieves(Block :: class_name(), $condition);
    }

    public static function truncate_home($user_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_USER), 
            new StaticConditionVariable($user_id));
        $tabs = self :: retrieves(Tab :: class_name(), $condition);
        
        while ($tab = $tabs->next_result())
        {
            if (! $tab->delete())
            {
                return false;
            }
        }
        
        return true;
    }

    public static function delete_home_block_configs($home_block)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(BlockConfiguration :: class_name(), BlockConfiguration :: PROPERTY_BLOCK_ID), 
            new StaticConditionVariable($home_block->get_id()));
        return self :: deletes(BlockConfiguration :: class_name(), $condition);
    }
}
