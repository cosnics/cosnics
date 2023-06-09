<?php
namespace Chamilo\Core\Home\Storage;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package home.lib This is a skeleton for a data manager for the Home application.
 * @author  Hans De Bisschop
 * @author  Dieter De Neef
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    public const PREFIX = 'home_';

    public static function determine_user_id()
    {
        /**
         * @var SessionInterface $session
         */
        $session = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);

        $current_user_id = $session->get(\Chamilo\Core\User\Manager::SESSION_USER_ID);
        $current_user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class, intval($current_user_id)
        );

        $user_home_allowed = Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_user_home']);
        $generalMode = $session->get('Chamilo\Core\Home\General');

        if ($current_user instanceof User)
        {
            if ($generalMode && $current_user->is_platform_admin())
            {
                return 0;
            }
            elseif ($user_home_allowed)
            {
                return $current_user->getId();
            }
            elseif (!$user_home_allowed && $current_user->is_platform_admin())
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
        $homeIntegrations = Configuration::getInstance()->getIntegrationRegistrations(Manager::CONTEXT);
        $blocks = [];

        foreach ($homeIntegrations as $homeIntegration)
        {
            $className = $homeIntegration[Registration::PROPERTY_CONTEXT] . '\Manager';

            if (class_exists($className))
            {
                $homeIntegrationManager = new $className();
                $blockTypes = $homeIntegrationManager->getBlockTypes();

                $parentNamespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                    $homeIntegration[Registration::PROPERTY_TYPE]
                );
                $packageId = $homeIntegration[Registration::PROPERTY_CONTEXT];

                $packageGlyph = new NamespaceIdentGlyph(
                    $parentNamespace, true, false, false, IdentGlyph::SIZE_MINI, ['fa-fw']
                );

                $blocks[$packageId]['name'] = Translation::get('TypeName', null, $parentNamespace);
                $blocks[$packageId]['image'] = $packageGlyph->render();

                foreach ($blockTypes as $blockType)
                {
                    $blockName = ClassnameUtilities::getInstance()->getClassnameFromNamespace($blockType);

                    $blockGlyph = new NamespaceIdentGlyph(
                        $blockType, true, false, false, IdentGlyph::SIZE_MINI, ['fa-fw']
                    );

                    $blocks[$packageId]['components'][] = [
                        BlockRenderer::BLOCK_PROPERTY_ID => $blockType,
                        BlockRenderer::BLOCK_PROPERTY_NAME => Translation::get($blockName, null, $packageId),
                        BlockRenderer::BLOCK_PROPERTY_IMAGE => $blockGlyph->render()
                    ];
                }
            }
        }

        return $blocks;
    }
}
