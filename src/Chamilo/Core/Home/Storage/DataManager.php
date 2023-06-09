<?php
namespace Chamilo\Core\Home\Storage;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package home.lib This is a skeleton for a data manager for the Home application.
 * @author  Hans De Bisschop
 * @author  Dieter De Neef
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    public const PREFIX = 'home_';

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
