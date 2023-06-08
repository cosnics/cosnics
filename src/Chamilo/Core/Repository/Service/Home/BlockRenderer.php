<?php
namespace Chamilo\Core\Repository\Service\Home;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * Base class for blocks based on a content object.
 *
 * @copyright (c) 2011 University of Geneva
 * @license       GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author        lopprecht
 */
abstract class BlockRenderer extends \Chamilo\Core\Home\Renderer\BlockRenderer
{
    public const CONFIGURATION_OBJECT_ID = 'use_object';

    protected ContentObjectPublicationService $contentObjectPublicationService;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter,
        ContentObjectPublicationService $contentObjectPublicationService, ElementRightsService $elementRightsService
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator, $configurationConsulter, $elementRightsService);

        $this->contentObjectPublicationService = $contentObjectPublicationService;
    }

    public function displayContent(Element $block, ?User $user = null): string
    {
        return $this->isConfigured($block) ? $this->displayRepositoryContent($block) : $this->displayEmpty();
    }

    public function displayEmpty(): string
    {
        return $this->getTranslator()->trans('ConfigureBlockFirst', [], Manager::CONTEXT);
    }

    abstract public function displayRepositoryContent(Element $block): string;

    public function getConfigurationVariables(): array
    {
        return [];
    }

    public function getContentObjectConfigurationVariables(): array
    {
        return [self::CONFIGURATION_OBJECT_ID];
    }

    protected function getContentObjectPublication(Element $block): ?ContentObjectPublication
    {
        return $this->getContentObjectPublicationService()->getFirstContentObjectPublicationForElement($block);
    }

    public function getContentObjectPublicationService(): ContentObjectPublicationService
    {
        return $this->contentObjectPublicationService;
    }

    abstract protected function getDefaultTitle(): string;

    public function getObject(Element $block): ?ContentObject
    {
        $contentObjectPublication = $this->getContentObjectPublication($block);

        if ($contentObjectPublication instanceof ContentObjectPublication)
        {
            return $contentObjectPublication->getContentObject();
        }

        return null;
    }

    public function getObjectId(Element $block): ?int
    {
        $contentObjectPublication = $this->getContentObjectPublication($block);

        if ($contentObjectPublication instanceof ContentObjectPublication)
        {
            return $contentObjectPublication->get_content_object_id();
        }

        return null;
    }

    public function getTitle(Element $block, ?User $user = null): string
    {
        $content_object = $this->getObject($block);

        return empty($content_object) ? $this->getDefaultTitle() : $content_object->get_title();
    }

    /**
     * Return true if the block is linked to an object.
     * Otherwise returns false.
     */
    public function isConfigured(Element $block): bool
    {
        return $this->getObjectId($block) != 0;
    }

}
