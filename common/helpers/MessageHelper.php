<?php

namespace Bot\helpers;

use Longman\TelegramBot\Request;

class MessageHelper
{
    public static function parseUsers(string $text): array
    {
        $users = [];
        foreach (explode(' ', $text) as $item) {
            if ($item{1} === '@') {
                $item = self::getUserIdByNickname($item);
            }
            $users[] = $item;
        }

        return $users;
    }

    public static function getUserIdByNickname(string $nickname): int
    {
        return 123;
    }
}
