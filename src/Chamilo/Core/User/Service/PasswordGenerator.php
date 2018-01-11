<?php

namespace Chamilo\Core\User\Service;

/**
 * @package Chamilo\Core\User\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PasswordGenerator
{
    /**
     * @var array
     */
    protected $numbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * @var array
     */
    protected $letters = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
        'w', 'x', 'y', 'z'
    ];

    /**
     * @var array
     */
    protected $symbols = ['?', '=', '!'];

    /**
     * Generates a random password with $length characters
     *
     * @param int $length
     * @param bool $includeNumbers
     * @param bool $includeCapitalLetters
     * @param bool $includeSymbols
     *
     * @return string
     */
    public function generatePassword(
        $length = 8, $includeNumbers = true, $includeCapitalLetters = true, $includeSymbols = false
    )
    {
        $passwordPool = $this->letters;

        if($includeNumbers)
        {
            $passwordPool = array_merge($passwordPool, $this->numbers);
        }

        if($includeCapitalLetters)
        {
            $capitalLetters = [];
            foreach($this->letters as $letter)
            {
                $capitalLetters[] = strtoupper($letter);
            }

            $passwordPool = array_merge($passwordPool, $capitalLetters);
        }

        if($includeSymbols)
        {
            $passwordPool = array_merge($passwordPool, $this->symbols);
        }

        $passwordPoolLength = count($passwordPool);

        $password = '';

        for($counter = 0; $counter < $length; $counter++)
        {
            $randomElementIndex = rand(0, $passwordPoolLength);
            $password .= $passwordPool[$randomElementIndex];
        }

        return $password;
    }
}