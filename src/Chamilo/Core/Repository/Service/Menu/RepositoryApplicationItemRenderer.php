<?php
namespace Chamilo\Core\Repository\Service\Menu;

use Chamilo\Core\Menu\Service\Renderer\ApplicationItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;

/**
 * @package Chamilo\Core\Repository\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryApplicationItemRenderer extends ApplicationItemRenderer
{

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new NamespaceIdentGlyph(Manager::CONTEXT, false, false, false, IdentGlyph::SIZE_SMALL, ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('RepositoryItem', [], Manager::CONTEXT);
    }

    public function isSelected(Item $item, User $user): bool
    {
        $currentWorkspace = $this->getRequest()->query->get(
            Manager::PARAM_WORKSPACE_ID
        );

        return parent::isSelected($item, $user) && !isset($currentWorkspace);
    }
}