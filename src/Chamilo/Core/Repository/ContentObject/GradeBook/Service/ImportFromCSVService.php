<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\GradeBookImportException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\NotCSVFileException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\InvalidHeaderException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\DuplicateResultException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\NoValueException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\InvalidValueException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCSVImportColumnJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCSVImportScoreJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use Chamilo\Core\User\Storage\DataClass\User;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportFromCSVService
{
    const TYPE_SCORES = 'scores';
    const TYPE_SCORES_COMMENTS = 'scores_comments';

    const fields_validate = [
        'lastname' => ['key' => 'lastname', 'label' => 'lastname', 'type' => 'string'],
        'firstname' => ['key' => 'firstname', 'label' => 'firstname', 'type' => 'string'],
        'id' => ['key' => 'id', 'label' => 'id', 'type' => 'string']
    ];

    /**
     * @var GradeBookService
     */
    protected $gradeBookService;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(GradeBookService $gradeBookService)
    {
        $this->gradeBookService = $gradeBookService;
        $this->serializer = $this->createSerializer();
    }

    /**
     * @param string $file
     * @param string $importType
     * @param array $users
     *
     * @return array
     * @throws GradeBookImportException
     */
    public function processCSV(string $file, string $importType, array $users): array
    {
        $fileHandle = fopen($file, 'r');
        $header = fgetcsv($fileHandle, null, ';');

        if (!$header || count($header) == 1) {
            throw new NotCSVFileException();
        }

        if (array_slice($header, 0, 3) != ['lastname', 'firstname', 'id']) {
            throw new InvalidHeaderException();
        }

        if (count($header) < 4 || ($importType == self::TYPE_SCORES_COMMENTS && count($header) < 5)) {
            throw new InvalidHeaderException(); // todo: specify
        }

        $fields = array_merge(self::fields_validate, array());

        $isScoresComments = $importType == self::TYPE_SCORES_COMMENTS;

        if ($importType == $isScoresComments)
        {
            $header = array_slice($header, 0, 5);
        }

        foreach (array_slice($header, 3) as $index => $field_name)
        {
            $index = $index + 3;
            $type = 'score';
            if ($index == 4 && $isScoresComments)
            {
                $type = 'string';
            }
            $fields['col' . $index] = ['key' => ('col' . $index), 'label' => $field_name, 'type' => $type];
        }

        $results = [];
        $processedUserIds = [];
        $currentLine = 1;

        while (($row_tmp = fgetcsv($fileHandle, null, ';')) !== FALSE)
        {
            $currentLine++;

            if (count($row_tmp) === 1 && empty($row_tmp[0]))
            {
                continue;
            }

            $result = $this->processResults($currentLine, $row_tmp, $fields);
            $user = $this->findUser($result['id'], $users);
            if (!isset($user))
            {
                $result['valid'] = false;
            }
            else
            {
                $userId = (int) $user->getId();
                if (in_array($userId, $processedUserIds))
                {
                    throw new DuplicateResultException($currentLine, $result['lastname'], $result['firstname']);
                }
                $result['valid'] = true;
                $result['user_id'] = $userId;
                $processedUserIds[] = $userId;
            }
            $results[] = $result;
        }

        return ['fields' => array_values($fields), 'results' => $results];
    }

    /**
     * @param int $line
     * @param array $row_tmp
     * @param array $fields
     *
     * @return array
     * @throws GradeBookImportException
     */
    protected function processResults(int $line, array $row_tmp, array $fields): array
    {
        $result = array();
        foreach (array_keys($fields) as $index => $key)
        {
            $this->validateField($line, $key, $row_tmp[$index], $fields);
            $type = $fields[$key]['type'];
            $result[$key] = $row_tmp[$index];
            if ($type == 'score' && is_numeric($result[$key]))
            {
                $result[$key] = (float) $result[$key];
            }
            else if ($type == 'score' && empty($result[$key]))
            {
                $result[$key] = null;
            }
        }
        return $result;
    }

    /**
     * @param int $line
     * @param string $key
     * @param mixed $value
     * @param array $fields
     *
     * @throws GradeBookImportException
     */
    protected function validateField(int $line, string $key, $value, array $fields): void
    {
        $keys = array_keys($fields);
        $type = $fields[$key]['type'];
        if (empty($value))
        {
            if (array_key_exists($key, self::fields_validate))
            {
                throw new NoValueException($line, $key, $type);
            }
            return;
        }
        if (!$this->hasValidValue($type, $value))
        {
            $column = array_search($key, $keys) + 1;
            $field = $fields[$key]['label'];
            throw new InvalidValueException($line, $column, $field, $type, $value);
        }
    }

    /**
     * @param string $type
     * @param $value
     *
     * @return bool
     */
    protected function hasValidValue(string $type, $value): bool
    {
        if ($type == 'string' && !is_string($value))
        {
            return false;
        }
        if ($type == 'score' && !(is_numeric($value) || (is_string($value) && (strtolower($value) == 'gafw' || strtolower($value) == 'aabs'))))
        {
            return false;
        }
        return true;
    }

    /**
     * @param string $userId
     * @param User[] $users
     * @return User|null
     */
    protected function findUser(string $userId, array $users): ?User
    {
        foreach ($users as $user)
        {
            if (str_ends_with($user->get_official_code(), $userId) || $user->get_username() == $userId || $user->get_email() == $userId)
            {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param string $importJSONData
     * @param array $users
     *
     * @return array
     */
    public function importResults(GradeBookData $gradeBookData, string $importJSONData, array $users): array
    {
        $columnJsonModelData = $this->serializer->deserialize(
            $importJSONData, 'array<Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCSVImportColumnJSONModel>', 'json'
        );

        $missingUsers = [];

        foreach ($columnJsonModelData as $columnJsonModel)
        {
            if (!$columnJsonModel instanceof GradeBookCSVImportColumnJSONModel)
            {
                throw new \RuntimeException('Could not parse the gradebook csv column JSON model');
            }
            $column = $this->createGradeBookColumnFromImport($gradeBookData, $columnJsonModel);

            $scores = [];
            foreach ($columnJsonModel->getResults() as $scoreJsonModel)
            {
                if (!$scoreJsonModel instanceof GradeBookCSVImportScoreJSONModel)
                {
                    throw new \RuntimeException('Could not parse the gradebook csv score JSON model');
                }
                $scores[$scoreJsonModel->getId()] = $scoreJsonModel;
            }

            foreach ($users as $user)
            {
                if (array_key_exists($user->getId(), $scores))
                {
                    $this->createGradeBookScoreFromImport($gradeBookData, $column, $scores[$user->getId()]);
                }
                else
                {
                    if (!array_key_exists($user->getId(), $missingUsers))
                    {
                        $missingUsers[$user->getId()] = ['lastname' => $user->get_lastname(), 'firstname' => $user->get_firstname(), 'official_code' => $user->get_official_code()];
                    }
                    $score = new GradeBookScore();
                    $score->setGradeBookData($gradeBookData);
                    $score->setGradeBookColumn($column);
                    $score->setTargetUserId($user->getId());
                    $score->setOverwritten(false);
                }
            }
        }

        $this->gradeBookService->saveGradeBook($gradeBookData);

        return ['missing_users' => array_values($missingUsers)];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param GradeBookCSVImportColumnJSONModel $columnJsonModel
     *
     * @return GradeBookColumn
     */
    public function createGradeBookColumnFromImport(GradeBookData $gradeBookData, GradeBookCSVImportColumnJSONModel $columnJsonModel): GradeBookColumn
    {
        $column = new GradeBookColumn($gradeBookData);
        $column->setType(GradeBookColumn::TYPE_STANDALONE);
        $column->setTitle($columnJsonModel->getLabel());
        $column->setWeight(null);
        $column->setCountForEndResult(true);
        $column->setIsReleased(true);
        $column->setAuthPresenceEndResult(GradeBookColumn::NO_SCORE);
        $column->setUnauthPresenceEndResult(GradeBookColumn::MIN_SCORE);
        return $column;
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param GradeBookColumn $column
     * @param GradeBookCSVImportScoreJSONModel $scoreJsonModel
     *
     * @return GradeBookScore
     */
    public function createGradeBookScoreFromImport(GradeBookData $gradeBookData, GradeBookColumn $column, GradeBookCSVImportScoreJSONModel $scoreJsonModel): GradeBookScore
    {
        $score = new GradeBookScore();
        $score->setGradeBookData($gradeBookData);
        $score->setGradeBookColumn($column);
        $score->setTargetUserId($scoreJsonModel->getId());
        $score->setOverwritten(true);
        $score->setNewScore($scoreJsonModel->getScore());
        $score->setNewScoreAuthAbsent($scoreJsonModel->isAuthAbsent());
        $score->setComment($scoreJsonModel->getComment());
        return $score;
    }

    /**
     * @return Serializer
     */
    private function createSerializer(): Serializer
    {
        return SerializerBuilder::create()
            ->setDeserializationContextFactory(function () {
                return DeserializationContext::create();
            })
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->build();
    }
}
