<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Translation\Translation;

abstract class Action
{
    use DependencyInjectionContainerTrait;

    public const TYPE_CONFIRM = '2';
    public const TYPE_ERROR = '4';
    public const TYPE_NORMAL = '1';
    public const TYPE_WARNING = '3';

    private $message;

    public function __construct()
    {
        $this->message = [];
    }

    public function add_message($type = self::TYPE_NORMAL, $message)
    {
        switch ($type)
        {
            case self::TYPE_CONFIRM :
                $this->message[] = '<span style="color: green; font-weight: bold;">' . $message . '</span>';
                break;
            case self::TYPE_WARNING :
                $this->message[] = '<span style="color: orange; font-weight: bold;">' . $message . '</span>';
                break;
            case self::TYPE_ERROR :
                $this->message[] = '<span style="color: red; font-weight: bold;">' . $message . '</span>';
                break;
            default :
                $this->message[] = $message;
                break;
        }
    }

    public function failed($error_message)
    {
        $this->add_message(self::TYPE_ERROR, $error_message);
        $this->add_message(self::TYPE_ERROR, Translation::get($this->getType() . 'Failed'));

        return false;
    }

    public function getStorageUnitRepository(): StorageUnitRepository
    {
        return $this->getService(StorageUnitRepository::class);
    }

    public function getConfigurationService(): ConfigurationService
    {
        return $this->getService(ConfigurationService::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function getType()
    {
        return $this->getClassnameUtilities()->getClassnameFromObject($this);
    }

    /**
     * @return string[]
     */
    public function get_message()
    {
        return $this->message;
    }

    /**
     * @deprecated Use Action::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @return string
     */
    public function retrieve_message()
    {
        return implode('<br />' . PHP_EOL, $this->get_message());
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    public function successful()
    {
        $this->add_message(self::TYPE_CONFIRM, Translation::get($this->getType() . 'Successful'));

        return true;
    }
}
