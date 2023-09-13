<?php
namespace Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service;

use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * Class that describes the users for the rights editor
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserEntityProvider implements RightsEntityProvider
{
    public const ENTITY_NAME = 'user';
    public const ENTITY_TYPE = 1;

    /**
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function __construct(
        UserService $userService, Translator $translator, StringUtilities $stringUtilities
    )
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countEntityItems(Condition $condition = null)
    {
        return $this->getUserService()->countUsers($condition);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findEntityItems(
        Condition $condition = null, int $offset = null, int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->getUserService()->findUsers($condition, $offset, $count, $orderBy);
    }

    /**
     * @param int $entityIdentifier
     *
     * @return string
     */
    public function getEntityDescriptionByIdentifier(int $entityIdentifier)
    {
        $user = $this->getUserService()->findUserByIdentifier($entityIdentifier);

        if (!$user instanceof User)
        {
            return null;
        }

        return $user->get_official_code();
    }

    /**
     * @param int $entityIdentifier
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    public function getEntityElementFinderElement(string $entityIdentifier)
    {
        $user = $this->getUserService()->findUserByIdentifier($entityIdentifier);

        if (!$user instanceof User)
        {
            return null;
        }

        return new AdvancedElementFinderElement(
            static::ENTITY_TYPE . '_' . $entityIdentifier, $this->getEntityGlyph()->getClassNamesString(),
            $this->getEntityTitleByIdentifier($entityIdentifier),
            $this->getEntityDescriptionByIdentifier($entityIdentifier)
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType
     */
    public function getEntityElementFinderType()
    {
        return new AdvancedElementFinderElementType(
            'users', $this->getTranslator()->trans('Users', [], 'Chamilo\Core\User'), Manager::CONTEXT,
            'UserEntityFeed', []
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    public function getEntityGlyph()
    {
        return new FontAwesomeGlyph('user', [], $this->getEntityTranslatedName());
    }

    /**
     * @param int $userIdentifier
     *
     * @return int
     */
    public function getEntityItemIdentifiersForUserIdentifier($userIdentifier)
    {
        return [$userIdentifier];
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @param int $entityIdentifier
     *
     * @return string
     */
    public function getEntityTitleByIdentifier(int $entityIdentifier)
    {
        $user = $this->getUserService()->findUserByIdentifier($entityIdentifier);

        if (!$user instanceof User)
        {
            return null;
        }

        return $user->get_fullname();
    }

    /**
     * @return string
     */
    public function getEntityTranslatedName()
    {
        $variable = (string) $this->getStringUtilities()->createString(self::ENTITY_NAME)->upperCamelize();

        return $this->getTranslator()->trans($variable, [], 'Chamilo\Core\User');
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return static::ENTITY_TYPE;
    }

    /**
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities): void
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }
}
