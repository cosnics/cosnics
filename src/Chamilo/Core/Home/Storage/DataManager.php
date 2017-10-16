<?php
namespace Chamilo\Core\Home\Storage;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
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
        $current_user_id = \Chamilo\Libraries\Platform\Session\Session::get_user_id();
        $current_user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class_name(),
            intval($current_user_id));

        $user_home_allowed = Configuration::getInstance()->get_setting(array(Manager::context(), 'allow_user_home'));
        $generalMode = \Chamilo\Libraries\Platform\Session\Session::retrieve('Chamilo\Core\Home\General');

        if ($current_user instanceof User)
        {
            if ($generalMode && $current_user->is_platform_admin())
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

    public static function getPlatformBlocks()
    {
        $homeIntegrations = Configuration::getInstance()->getIntegrationRegistrations(Manager::package());
        $blocks = array();

        foreach ($homeIntegrations as $homeIntegration)
        {
            $className = $homeIntegration[Registration::PROPERTY_CONTEXT] . '\Manager';

            if (class_exists($className))
            {
                $homeIntegrationManager = new $className();
                $blockTypes = $homeIntegrationManager->getBlockTypes();

                foreach ($blockTypes as $blockType)
                {
                    $parentNamespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                        $homeIntegration[Registration::PROPERTY_TYPE]);
                    $blockName = ClassnameUtilities::getInstance()->getClassnameFromNamespace($blockType);

                    $packageId = $homeIntegration[Registration::PROPERTY_CONTEXT];
                    $blockId = $blockType;

                    $blocks[$packageId]['name'] = Translation::get('TypeName', null, $parentNamespace);

                    $blocks[$packageId]['image'] = Theme::getInstance()->getImagePath($parentNamespace, 'Logo/16');

                    $blocks[$packageId]['components'][] = array(
                        BlockRenderer::BLOCK_PROPERTY_ID => $blockId,
                        BlockRenderer::BLOCK_PROPERTY_NAME => Translation::get($blockName, null, $packageId),
                        BlockRenderer::BLOCK_PROPERTY_IMAGE => BlockRenderer::getImagePath($packageId, $blockName));
                }
            }
        }

        return $blocks;
    }

    public static function retrieveTabBlocks($tabElement)
    {
        $columnCondition = new EqualityCondition(
            new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_PARENT_ID),
            new StaticConditionVariable($tabElement->getId()));

        $columnIdentifiers = self::distinct(
            Element::class_name(),
            new DataClassDistinctParameters($columnCondition, Element::PROPERTY_ID));

        $condition = new InCondition(
            new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_PARENT_ID),
            $columnIdentifiers);

        return self::retrieves(Block::class_name(), new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param integer $userIdentifier
     * @return boolean
     */
    public static function truncateHome($userIdentifier)
    {
        return self::deletes(
            Element::class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_USER_ID),
                new StaticConditionVariable($userIdentifier)));
    }
}
