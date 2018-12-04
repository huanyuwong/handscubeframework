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

    /**
     * Drop string index.
     *[ "foo" => "bar"]  --> [ 0 => "bar"]
     * @param array $arr
     * @return void
     */
    public static function dropStringKey(array $arr)
    {
        $newArr = [];
        foreach ($arr as $item) {
            $newArr[] = $item;
        }
        return $newArr;
    }

    /**
     * Sort with arr key.
     * e.g. ["b","a","c"] + ["a"=>"apple","c"=>"cache","b"=>"boring"] => ["b"=>"boring", "a"=>"apple", "c"=>"cache"];
     *
     * @param array $sortKeys
     * @param array $sortArr
     * @return void
     */
    public static function sortWithKeyArray(array $sortKeys, array $sortArr)
    {
        $newSortArr = [];
        foreach ($sortKeys as $key) {
            $newSortArr[$key] = isset($sortArr[$key]) ? $sortArr[$key] : null;
        }
        return $newSortArr;
    }

    /**
     * Clear array.
     *
     * @param array $arr
     * @return void
     */
    public static function clearArr(array &$arr)
    {
        foreach ($arr as $v) {
            array_pop($arr);
        }
    }
}
