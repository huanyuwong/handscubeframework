<?php

namespace Handscube\Assistants;

class Arr {

    static function splitWith(string $delimeter, array $targetArray){
        $result = [];
        foreach($targetArray as $item) {
            if(!empty($item)){
                $itemResult = explode($delimeter, $item);
                @$result[trim($itemResult[0])] = @trim($itemResult[1]);
            }
            
        }
        return $result;
    }
}
