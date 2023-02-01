<?php
namespace Chamilo\Core\Metadata\Vocabulary;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\Metadata\Vocabulary
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DEFAULT = 'Default';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_UPDATE = 'Updater';
    public const ACTION_USER = 'User';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'vocabulary_action';
    public const PARAM_USER_ID = 'user_id';
    public const PARAM_VOCABULARY_ID = 'vocabulary_id';

    public function getDataClassEntityFactory(): DataClassEntityFactory
    {
        return $this->getService(DataClassEntityFactory::class);
    }

    public function getEntityTranslationService(): EntityTranslationService
    {
        return $this->getService(EntityTranslationService::class);
    }

    public function getSelectedElementId(): string
    {
        return $this->getRequest()->query->get(\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID);
    }

    public function getSelectedUserId(): string
    {
        return $this->getRequest()->query->get(self::PARAM_USER_ID, '0');
    }
}
