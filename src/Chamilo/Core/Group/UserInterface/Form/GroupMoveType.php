<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\VisualContentType;
use Chamilo\Libraries\UserInterface\Form\Architecture\FormHelperTrait;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMoveType extends AbstractType
{
    use FormHelperTrait;

    public const string OPTION_DISABLED_IDENTIFIERS = 'disabledGroupIdentifiers';
    public const string OPTION_EXCLUDED_IDENTIFIERS = 'excludedGroupIdentifiers';

    protected GroupService $groupService;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected OptionsTreeRenderer $optionsTreeRenderer;

    public function __construct(
        Translator $translator, OptionsTreeRenderer $optionsTreeRenderer, GroupsTreeTraverser $groupsTreeTraverser,
        GroupService $groupService, ResourceManager $resourceManager, WebPathBuilder $webPathBuilder
    )
    {
        $this->translator = $translator;
        $this->optionsTreeRenderer = $optionsTreeRenderer;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->groupService = $groupService;
        $this->resourceManager = $resourceManager;
        $this->webPathBuilder = $webPathBuilder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $translator = $this->getTranslator();

        $name =
            $this->createText($builder, Group::PROPERTY_NAME, $translator->trans('Name', [], Manager::CONTEXT), false)
                ->setDisabled(true);
        $builder->add($name);

        $builder->add(
            'visual', VisualContentType::class, [
                'label' => $translator->trans('Visual', [], Manager::CONTEXT),
                'content' => 'Just some visual content that looks like a form element',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ]
        );

        $this->addCategory(
            $builder, 'category', $translator->trans('CategoryTitle', [], Manager::CONTEXT)
        );

        $this->addDanger(
            $builder, 'message', 'This is where the message content should go',
            $translator->trans('MessageTitle', [], Manager::CONTEXT)
        );

        $this->addHtml(
            $builder, 'test', '<div class="alert alert-danger mb-3">Sample Html Alert</div>'
        );

        $this->addHtmlEditor($builder, 'description', 'HtmlEditorLabel');

        $this->addSelect(
            $builder, NestedSet::PROPERTY_PARENT_ID, $translator->trans('NewLocation', [], Manager::CONTEXT), true,
            $this->getOptionsTreeRenderer()->getOptions(
                disabledIdentifiers: $this->determineDisabledGroupIdentifiers(
                    $options[self::OPTION_DISABLED_IDENTIFIERS]
                )
            )->toArray()
        );

        $this->addSaveAndResetButton($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            self::OPTION_EXCLUDED_IDENTIFIERS => [],
            self::OPTION_DISABLED_IDENTIFIERS => []
        ]);

        $normalizer = static function (Options $options, $identifiers) {
            if (!is_array($identifiers)) {
                throw new LogicException('identifiers should be an array.');
            }

            foreach ($identifiers as $identifier) {
                if (!Uuid::isValid($identifier)) {
                    throw new LogicException(
                        'identifier should be a valid UUID.'
                    );
                }
            }

            return $identifiers;
        };

        $resolver->setNormalizer(self::OPTION_EXCLUDED_IDENTIFIERS, $normalizer);
        $resolver->setNormalizer(self::OPTION_DISABLED_IDENTIFIERS, $normalizer);
    }

    protected function determineDisabledGroupIdentifiers(array $rootDisabledGroupIdentifiers = []): array
    {
        $disabledGroupIdentifiers = [];

        foreach ($rootDisabledGroupIdentifiers as $rootDisabledGroupIdentifier) {
            try {
                $disabledGroup = $this->getGroupService()->findGroupByIdentifier($rootDisabledGroupIdentifier);
                $disabledSubgroupIdentifiers =
                    $this->getGroupsTreeTraverser()->findSubGroupIdentifiersForGroup($disabledGroup, true);

                $disabledGroupIdentifiers[] = $rootDisabledGroupIdentifier;
                $disabledGroupIdentifiers = array_merge($disabledGroupIdentifiers, $disabledSubgroupIdentifiers);
            }
            catch (StorageMethodException|StorageNoResultException) {
            }
        }

        return $disabledGroupIdentifiers;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    public function getOptionsTreeRenderer(): OptionsTreeRenderer
    {
        return $this->optionsTreeRenderer;
    }
}