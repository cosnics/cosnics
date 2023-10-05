<?php
namespace Chamilo\Core\Admin\Announcement\Service\Home;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockRendererInterface;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Core\Home\Form\ConfigurationFormFactory;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

class SystemAnnouncementsBlockRenderer extends BlockRenderer implements ConfigurableBlockRendererInterface
{
    public const CONFIGURATION_SHOW_EMPTY = 'show_when_empty';

    public const CONTEXT = Manager::CONTEXT;

    protected PublicationService $publicationService;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, PublicationService $publicationService,
        ElementRightsService $elementRightsService, ConfigurationFormFactory $configurationFormFactory
    )
    {
        parent::__construct(
            $homeService, $urlGenerator, $translator, $configurationConsulter, $elementRightsService,
            $configurationFormFactory
        );

        $this->publicationService = $publicationService;
    }

    /**
     * @throws \QuickformException
     */
    public function addConfigurationFieldsToForm(ConfigurationForm $configurationForm, Element $block): void
    {
        $translator = $this->getTranslator();

        $group = [];
        $group[] = $configurationForm->createElement(
            'radio', self::CONFIGURATION_SHOW_EMPTY, null, $translator->trans('True', [], StringUtilities::LIBRARIES), 1
        );
        $group[] = $configurationForm->createElement(
            'radio', self::CONFIGURATION_SHOW_EMPTY, null, $translator->trans('False', [], StringUtilities::LIBRARIES),
            0
        );

        $configurationForm->addGroup(
            $group, self::CONFIGURATION_SHOW_EMPTY, $translator->trans('ShowWhenEmpty', [], StringUtilities::LIBRARIES),
            '', false
        );

        $configurationForm->setDefaults(
            [self::CONFIGURATION_SHOW_EMPTY => $block->getSetting(self::CONFIGURATION_SHOW_EMPTY, 1)]
        );
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = [];

        $publications = $this->getPublications($user);

        if ($publications->count() == 0)
        {
            $html[] = '<div class="panel-body portal-block-content">';
            $html[] =
                htmlspecialchars($this->getTranslator()->trans('NoSystemAnnouncementsCurrently', [], Manager::CONTEXT));
            $html[] = '</div>';
        }

        foreach ($publications as $publication)
        {
            $content_object = DataManager::retrieve_by_id(
                ContentObject::class, $publication[Publication::PROPERTY_CONTENT_OBJECT_ID]
            );

            $icon = $content_object->get_icon_image(IdentGlyph::SIZE_MINI, !$publication[Publication::PROPERTY_HIDDEN]);

            $href = htmlspecialchars($this->getPublicationLink($publication));
            $title = htmlspecialchars($content_object->get_title());
            $link = '<a href="' . $href . '">' . $title . '</a>';

            $html[] = '<div class="list-group-item">';
            $html[] = '<span class="pull-right">' . $icon . '</span>';
            $html[] = $link;
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function getConfigurationVariables(): array
    {
        return [self::CONFIGURATION_SHOW_EMPTY];
    }

    public function getPublicationLink($publication): string
    {
        $parameters = [];

        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Admin\Manager::CONTEXT;
        $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Admin\Manager::ACTION_SYSTEM_ANNOUNCEMENTS;
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW;
        $parameters[Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID] = $publication[DataClass::PROPERTY_ID];

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    protected function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getPublications(User $user): ArrayCollection
    {
        return $this->getPublicationService()->findVisiblePublicationRecordsForUserIdentifier($user->getId());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function isEmpty(User $user): bool
    {
        return $this->getPublications($user)->count() == 0;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function isVisible(Element $block, ?User $user = null): bool
    {
        if (!parent::isVisible($block, $user) || ($this->isEmpty($user) && !$this->showWhenEmpty($block)))
        {
            return false;
        }

        return true;
    }

    public function renderContentFooter(Element $block): string
    {
        return '</div>';
    }

    public function renderContentHeader(Element $block): string
    {
        return '<div class="list-group portal-block-content' . ($block->isVisible() ? '' : ' hidden') . '">';
    }

    public function showWhenEmpty(Element $block): bool
    {
        return (bool) $block->getSetting(self::CONFIGURATION_SHOW_EMPTY, true);
    }
}
