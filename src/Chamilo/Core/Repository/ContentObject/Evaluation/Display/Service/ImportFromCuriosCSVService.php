<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\DuplicateFieldsException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\DuplicateResultException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\InvalidValueException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\LanguageNotDetectedException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\MissingFieldsException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\NotCSVFileException;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\NoValueException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use DateTime;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportFromCuriosCSVService
{
    const STATE_HEADERS = 0;
    const STATE_RESULTS = 1;
    const STATE_STATS = 2;

    const properties = [
        'en' => [
            'surname' => 'surname',
            'name' => 'name',
            'id' => 'id',
            'email' => 'email',
            'gender' => 'gender',
            'lang_msg_started_at' => 'started',
            'lang_msg_stopped_at' => 'stopped',
            'lang_msg_total_score' => 'total_score',
            'lang_msg_total_percentage' => 'total_percentage'
        ],
        'nl' => [
            'familienaam' => 'surname',
            'naam' => 'name',
            'id' => 'id',
            'email' => 'email',
            'gender' => 'gender',
            'gestart op' => 'started',
            'gestopt op' => 'stopped',
            'totale score' => 'total_score',
            'totaal percentage' => 'total_percentage'
        ],
    ];

    const check_should_be_present = ['surname', 'name', 'id', 'started', 'stopped', 'total_score', 'total_percentage'];
    const check_should_keep = ['surname', 'name', 'id', 'total_percentage'];

    const check_validate = [
        'surname' => 'string',
        'name' => 'string',
        'id' => 'string',
        'started' => 'date',
        'stopped' => 'date',
        'total_score' => 'number',
        'total_percentage' => 'number'
    ];

    /**
     * @param string $file
     * @param array $users
     * @return array
     * @throws UserException
     */
    public function processCSV(string $file, array $users): array
    {
        $fileHandle = fopen($file, 'r');
        $state = self::STATE_HEADERS;
        $header = fgetcsv($fileHandle, null, ';');

        if (!$header || count($header) == 1)
        {
            throw new NotCSVFileException();
        }

        $filteredField = array_filter(array_map('strtolower', $header));
        if (count($filteredField) !== count(array_unique($filteredField)))
        {
            throw new DuplicateFieldsException(1, $this->getDuplicateFields($filteredField));
        }

        $lang = $this->checkLanguage($filteredField);
        if (empty($lang))
        {
            throw new LanguageNotDetectedException(1);
        }

        $data = $this->getData($filteredField, $lang, $header);

        $missingFields = array_diff(self::check_should_be_present, array_keys($data));

        if (!empty($missingFields))
        {
            throw new MissingFieldsException(1, $this->getMissingNames($lang, $missingFields));
        }

        $fields = $this->getFieldData($data);
        $results = [];
        $stats = [];

        $processedUserIds = [];

        $currentLine = 1;
        while (($row_tmp = fgetcsv($fileHandle, null, ';')) !== FALSE)
        {
            $currentLine++;
            if (count($row_tmp) === 1 && empty($row_tmp[0]))
            {
                $state = self::STATE_STATS;
                continue;
            }

            if ($state === self::STATE_HEADERS && !empty($row_tmp[2])) // $row_tmp ID field has a value
            {
                $state = self::STATE_RESULTS;
            }

            switch ($state)
            {
                case self::STATE_STATS:
                    $stat = $this->processStat($currentLine, $row_tmp, $data);
                    $stats[] = $stat;
                    break;
                case self::STATE_RESULTS:
                    $result = $this->processResults($currentLine, $row_tmp, $data);
                    $user = $this->findUser($result['id'], $users);
                    if (!isset($user))
                    {
                        $result['valid'] = false;
                    }
                    else
                    {
                        if (in_array($user['id'], $processedUserIds))
                        {
                            throw new DuplicateResultException($currentLine, $result['surname'], $result['name']);
                        }
                        $result['valid'] = true;
                        $result['user_id'] = $user['id'];
                        $processedUserIds[] = $user['id'];
                    }
                    $results[] = $result;
                    break;
            }
        }
        return ['fields' => $fields, 'results' => $results, 'stats' => $stats];
    }

    /**
     * @param array $filteredField
     * @return array
     */
    protected function getDuplicateFields(array $filteredField): array
    {
        $foundFields = [];
        $duplicateFields = [];
        foreach ($filteredField as $field)
        {
            if (in_array($field, $foundFields))
            {
                $duplicateFields[] = $field;
            }
            $foundFields[] = $field;
        }
        return $duplicateFields;
    }

    /**
     * @param array $filteredHeader
     * @return string
     */
    protected function checkLanguage(array $filteredHeader): string
    {
        if (in_array('familienaam', $filteredHeader))
        {
            return 'nl';
        }
        if (in_array('surname', $filteredHeader))
        {
            return 'en';
        }
        return '';
    }

    /**
     * @param array $filteredHeader
     * @param string $lang
     * @param $header
     * @return array
     */
    protected function getData(array $filteredHeader, string $lang, $header): array
    {
        $data = array();
        $usedDisplayKeys = array();
        foreach ($filteredHeader as $index => $prop)
        {
            $key = $this->getKey($lang, $prop);

            if (isset($key))
            {
                $displayKey = $key;
                $i = 1;
                while (in_array($displayKey, $usedDisplayKeys))
                {
                    $i++;
                    $displayKey = $key . '_' . $i;
                }
                $data[$displayKey] = ['key' => $key, 'displayKey' => $displayKey, 'displayName' => $header[$index], 'index' => $index];
                $usedDisplayKeys[] = $displayKey;
            }
        }
        return $data;
    }

    /**
     * @param string $lang
     * @param $prop
     * @return string|null
     */
    protected function getKey(string $lang, $prop)
    {
        if (($lang == 'nl' && str_starts_with($prop, 'totale score')) || ($lang == 'en' && str_starts_with($prop, 'lang_msg_total_score')))
        {
            return 'total_score';
        }

        if (($lang == 'nl' && str_starts_with($prop, 'totaal percentage')) || ($lang == 'en' && str_starts_with($prop, 'lang_msg_total_percentage')))
        {
            return 'total_percentage';
        }

        if ($lang == 'nl')
        {
            return self::properties['nl'][$prop];
        }

        if ($lang == 'en')
        {
            return self::properties['en'][$prop];
        }

        return null;
    }

    /**
     * @param string $lang
     * @param array $missingFields
     * @return array|int[]|string[]
     */
    protected function getMissingNames(string $lang, array $missingFields): array
    {
        $props = array_flip(self::properties[$lang]);

        $getFieldName = function ($field) use ($props)
        {
            return $props[$field];
        };

        return array_map($getFieldName, $missingFields);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getFieldData(array $data): array
    {
        $filterPercentage = function ($item)
        {
            return $item['key'] == 'total_percentage';
        };

        $getDisplayKey = function ($item)
        {
            return $item['displayKey'];
        };

        $getDataFields = function ($displayKey) use ($data)
        {
            return ['key' => $displayKey, 'label' => $data[$displayKey]['displayName']];
        };

        $scoreKeys = array_map($getDisplayKey, array_filter($data, $filterPercentage));
        $displayKeys = array_merge(['surname', 'name', 'id'], $scoreKeys);
        return array_values(array_map($getDataFields, $displayKeys));
    }

    /**
     * @param int $line
     * @param array $row_tmp
     * @param array $data
     * @param bool $isStats
     * @return array
     * @throws UserException
     */
    protected function processResults(int $line, array $row_tmp, array $data, bool $isStats = false): array
    {
        $validateKeys = array_keys(self::check_validate);
        $result = [];
        foreach ($data as $item)
        {
            if (in_array($item['key'], $validateKeys))
            {
                $this->validateField($line, $item['displayKey'], $row_tmp[$item['index']], self::check_validate[$item['key']], $isStats);
            }
            if (in_array($item['key'], self::check_should_keep))
            {
                $result[$item['displayKey']] = $row_tmp[$item['index']];
            }
        }
        return $result;
    }

    /**
     * @param int $line
     * @param array $row_tmp
     * @param array $data
     * @return array
     * @throws UserException
     */
    protected function processStat(int $line, array $row_tmp, array $data): array
    {
        $result = $this->processResults($line, $row_tmp, $data, true);
        $result['valid'] = true;
        $result['isStat'] = true;
        return $result;
    }

    /**
     * @param int $line
     * @param string $name
     * @param $value
     * @param string $type
     * @param bool $isStats
     * @throws UserException
     */
    protected function validateField(int $line, string $name, $value, string $type, bool $isStats): void
    {
        if (!$isStats || ($isStats && $type == 'number'))
        {
            if (empty($value))
            {
                throw new NoValueException($line, $name, $type);
            }

            $valid = true;

            if ($valid && $type == 'number' && !is_numeric($value))
            {
                $valid = false;
            }

            if ($valid && $type == 'string' && (!is_string($value) || (is_numeric($value) && $name !== 'id')))
            {
                $valid = false;
            }

            if ($valid && $type == 'date' && !$this->validateDate($value))
            {
                $valid = false;
            }

            if (!$valid)
            {
                throw new InvalidValueException($line, $name, $type, $value);
            }
        }
    }

    /**
     * @param string $date
     * @return bool
     */
    protected function validateDate(string $date): bool
    {
        if ($date === '0000-00-00 00:00:00')
        {
            return true;
        }

        $format = 'Y-m-d H:i:s';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * @param string $userId
     * @param array $users
     * @return array|null
     */
    protected function findUser(string $userId, array $users)
    {
        foreach ($users as $user)
        {
            if (str_ends_with($user['official_code'], $userId))
            {
                return $user;
            }
        }
        return null;
    }
}