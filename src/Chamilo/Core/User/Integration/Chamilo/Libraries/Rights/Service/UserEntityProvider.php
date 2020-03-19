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
    const ENTITY_NAME = 'user';
    const ENTITY_TYPE = 1;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

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
     * @return integer
     */
    public function countEntityItems(Condition $condition = null)
    {
        return $this->getUserService()->countUsers($condition);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findEntityItems(
        Condition $condition = null, int $offset = null, int $count = null, array $orderProperties = null
    )
    {
        return $this->getUserService()->findUsers($condition, $offset, $count, $orderProperties);
    }

    /**
     * @param integer $entityIdentifier
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
     * @param integer $entityIdentifier
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    public function getEntityElementFinderElement(int $entityIdentifier)
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
            'users', $this->getTranslator()->trans('Users', [], 'Chamilo\Core\User'), Manager::context(),
            'UserEntityFeed', array()
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
     * @return integer[]
     */
    public function getEntityItemIdentifiersForUserIdentifier($userIdentifier)
    {
        return array($userIdentifier);
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @param integer $entityIdentifier
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
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities): void
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }
}
