<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass
 */
class HotspotQuestion extends ContentObject implements VersionableInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\HotspotQuestion';

    public const PROPERTY_ANSWERS = 'answers';
    public const PROPERTY_IMAGE = 'image';

    public function add_answer($answer)
    {
        $answers = $this->get_answers();
        $answers[] = $answer;

        return $this->setAdditionalProperty(self::PROPERTY_ANSWERS, serialize($answers));
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_ANSWERS, self::PROPERTY_IMAGE];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_hotspot_question';
    }

    /**
     * @return HotspotQuestionAnswer[]
     */
    public function get_answers()
    {
        if ($result = unserialize($this->getAdditionalProperty(self::PROPERTY_ANSWERS)))
        {
            return $result;
        }

        return [];
    }

    public function get_default_weight()
    {
        return $this->get_maximum_score();
    }

    public function get_image()
    {
        return $this->getAdditionalProperty(self::PROPERTY_IMAGE);
    }

    public function get_image_object()
    {
        $image = $this->get_image();

        if (isset($image) && $image != 0)
        {
            return DataManager::retrieve_by_id(ContentObject::class, $image);
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the maximum weight/score a user can receive.
     */
    public function get_maximum_score()
    {
        $max = 0;
        $answers = $this->get_answers();
        foreach ($answers as $answer)
        {
            $max += $answer->get_weight();
        }

        return $max;
    }

    public function get_number_of_answers()
    {
        return count($this->get_answers());
    }

    public function set_answers($answers)
    {
        return $this->setAdditionalProperty(self::PROPERTY_ANSWERS, serialize($answers));
    }

    // TODO: should be moved to an additional parent layer "question" which offers a default implementation.

    public function set_image($image)
    {
        $this->setAdditionalProperty(self::PROPERTY_IMAGE, $image);
    }
}
