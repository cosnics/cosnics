<?php
namespace Chamilo\Core\User\Service;

/**
 * @package Chamilo\Core\User\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class PasswordGenerator
{

    protected array $letters = [
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z'
    ];

    protected array $numbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    protected array $symbols = ['?', '=', '!'];

    public function generatePassword(
        int $length = 8, bool $includeNumbers = true, bool $includeCapitalLetters = true, bool $includeSymbols = false
    ): string
    {
        $passwordPool = $this->letters;

        if ($includeNumbers)
        {
            $passwordPool = array_merge($passwordPool, $this->numbers);
        }

        if ($includeCapitalLetters)
        {
            $capitalLetters = [];
            foreach ($this->letters as $letter)
            {
                $capitalLetters[] = strtoupper($letter);
            }

            $passwordPool = array_merge($passwordPool, $capitalLetters);
        }

        if ($includeSymbols)
        {
            $passwordPool = array_merge($passwordPool, $this->symbols);
        }

        $passwordPoolLength = count($passwordPool);

        $password = '';

        for ($counter = 0; $counter < $length; $counter ++)
        {
            $randomElementIndex = rand(0, $passwordPoolLength);
            $password .= $passwordPool[$randomElementIndex];
        }

        return $password;
    }
}