<?php


namespace App;


class AppUtil
{
    public static function page($page, $maxPage = null)
    {
        // $page = AppUtil::page(filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT));
        if ($page === false || $page === null || $page < 1) {
            $page = 1;
        }
        $page--;
        if ($maxPage !== null && $page > $maxPage) $page = 1;
        return (int)$page;
    }

    public static function pageMax($count, $perPage)
    {
        return (int)max(0, floor(($count - 1) / $perPage));
    }

    public static function pageView($page, $maxPage, $pc = 2)
    {
        $start = max(0, $page - $pc);
        $end = min($maxPage, $page + $pc);
        if ($start > 0) yield ['page' => 1, 'text' => '&laquo;', 'active' => false];

        for ($i = $start; $i <= $end; $i++) {
            yield ['page' => $i + 1, 'text' => (string)($i + 1), 'active' => $i == $page];
        }
        if ($end < $maxPage) yield ['page' => $maxPage + 1, 'text' => '&raquo;', 'active' => false];
    }

    public static function validateAssocSimple($assoc, $fields = [], $only = false)
    {
        if (!is_array($assoc)) return false;
        foreach ($fields as $fieldName => $fieldRequired) {
            if (
                !array_key_exists($fieldName, $assoc)
                || !is_string($assoc[$fieldName])
                || ($len = mb_strlen($assoc[$fieldName], 'UTF-8')) > 200
                || ($fieldRequired && $len < 1)
            ) return false;
        }
        if ($only) {
            foreach ($assoc as $assocKey => $_) {
                if (!array_key_exists($assocKey, $fields)) return false;
            }
        }
        return true;
    }
}