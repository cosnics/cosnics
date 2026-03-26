<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Form\Service\GroupMoveFormDataMapper;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
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
class GroupMoveFormType extends AbstractType
{
    public const string OPTION_DISABLED_IDENTIFIERS = 'disabledGroupIdentifiers';
    public const string OPTION_EXCLUDED_IDENTIFIERS = 'excludedGroupIdentifiers';

    public function __construct(
        protected readonly FormTypeBuilder $formTypeBuilder,
        protected readonly FormButtonTypeBuilder $formButtonTypeBuilder, protected readonly Translator $translator,
        protected readonly OptionsTreeRenderer $optionsTreeRenderer,
        protected readonly GroupsTreeTraverser $groupsTreeTraverser, protected readonly GroupService $groupService,
        protected readonly GroupMoveFormDataMapper $groupMoveFormDataMapper
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper($this->groupMoveFormDataMapper);

        $translator = $this->getTranslator();

        $name = $this->formTypeBuilder->createText(
            $builder, Group::PROPERTY_NAME, $translator->trans('Name', [], Manager::CONTEXT), false
        )->setDisabled(true);
        $builder->add($name);

        $builder->add(
            $this->formTypeBuilder->createSelect(
                $builder, NestedSet::PROPERTY_PARENT_ID, $translator->trans('NewLocation', [], Manager::CONTEXT), true,
                $this->getOptionsTreeRenderer()->getOptions(
                    disabledIdentifiers: $this->determineDisabledGroupIdentifiers(
                        $options[self::OPTION_DISABLED_IDENTIFIERS]
                    )
                )->toArray()
            )
        );

        $this->getFormButtonTypeBuilder()->addSaveAndResetButton($builder);
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

    public function getFormButtonTypeBuilder(): FormButtonTypeBuilder
    {
        return $this->formButtonTypeBuilder;
    }

    public function getFormTypeBuilder(): FormTypeBuilder
    {
        return $this->formTypeBuilder;
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

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}