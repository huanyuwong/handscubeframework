<?php

namespace Handscube\Assistants;

class Arr
{

    //use delimeter to split an array.
    public static function splitWith(string $delimeter, array $targetArray)
    {
        $result = [];
        foreach ($targetArray as $item) {
            if (!empty($item)) {
                $itemResult = explode($delimeter, $item);
                @$result[trim($itemResult[0])] = @trim($itemResult[1]);
            }

        }
        return $result;
    }

    public static function filter(array $needed, $filterBy = '')
    {

        foreach ($needed as $k => $v) {
            $k = preg_replace('/^(&nbsp;|\s)*|(&nbsp;|\s)*$/', '', $k);
            if ($k) {
                $arr[$k] = $v;
            }
        }
        return $arr;

    }

    /**
     * drop useless element.
     *
     * @param array $arr
     * @param string $target
     * @return void
     */
    public static function drop(array $arr, $target = '')
    {
        $filter = [];
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                if ($v == $target) {
                    continue;
                }
                $filter[$k] = $v;
            }
            return $filter;
        }
        return false;
    }
}
