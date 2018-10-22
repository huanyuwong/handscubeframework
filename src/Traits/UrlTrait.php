<?php

namespace Handscube\Traits;

use Handscube\Assistants\Arr;

trait UrlTrait
{
    /**
     * @Param $url [String] e.g. http[s]://test.com:8081?id=1&name=jim
     * @return [Array] Return parsed url arr.['host','METHOD','']
     * @temp var @matchs [Array]
     * Array
     *(
     *    [0] => //test.com:8081?id=1&name=jim
     *    [1] => //
     *    [2] => test.com:8081?id=1&name=jim
     *)
     */
    function parseUrl($url = '')
    {
        $url = $url ? $url : $this->url;
        preg_match('/(\/\/)(.*)/i', $url, $matches);
        $detail = [];
        if (strpos($matches[2], '?') !== false) {
            $detail = explode('?', @$matches[2]); //$detail = [ [0] => test.com:8081 [1] => id=1&name=jim ]
            $query = explode('&', @$detail[1]); // [0 => id=1;1 => name=jim]
            foreach ($query as $k => $v) {
                if (strpos($v, "=") === false) {
                    throw new \Handscube\Kernel\Exceptions\InsideException("Parameter query that does not conform to rules");
                }
                $splitArr = explode('=', $v);
                @$this->query[$splitArr[0]] = $splitArr[1]; //query [id=>1,name=>jim]
            }
            $this->query = Arr::filter($this->query); //Request query parameters
            unset($splitArr);
        } else {
            $detail[] = $matches[2]; //$detail = [ [0] => test.com:8081 ]
        }
        preg_match('/(www\.)?(.*)/i', $detail[0], $matches2); //Remove 'www.' if it exists.
        /*
        $mathcs2
        (
        [0] => test.com:8081/index/index/test
        [1] =>
        [2] => test.com:8081/index/index/test
         */
        $result = explode('/', $matches2[2]);
        $this->host = $this->host ? $this->host : array_pop($matches2);
        $this->module = $result[1] ? $result[1] : 'index';
        $this->controller = array_key_exists(2, $result) ? $result[2] : 'index';
        $this->action = array_key_exists(3, $result) ? $result[3] : 'index';

        for ($i = 4; $i <= count($result) - 1; $i++) {
            @$this->pathParams[] = $result[$i];
        }
        return ['host' => $this->host, 'module' => $this->module, 'controller' => $this->controller, 'action' => $this->action, 'path_pars' => $this->pathParams, 'query' => $this->query];

    }
}
