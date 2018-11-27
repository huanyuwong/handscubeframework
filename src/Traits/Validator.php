<?php

namespace Handscube\Traits;

use Handscube\Kernel\Exceptions\InvalidException;

/**
 * Trait Validator [c] Handscube.
 * @atuhor J.W.
 */

trait Validator
{

    protected $rules = [];
    /**
     * [
     *      'requird' => [
     *          'function' => function(){
     *          },
     *          'error' => 'error message',
     *          'success' => 'success message',
     *          'errorHandler => Closure | Function,
     *          'successHandler' => Clousre | Function
     *      ]
     *
     * ]
     */
    protected $properties = [];

    protected $errors = [];

    // public function __get($key)
    // {
    //     return isset($this->properties[$key]) ?: null;
    // }

    public function __set($key, $value)
    {
        if (\is_callable($value)) {
            $this->rules[$key]['function'] = $value;
            return true;
        }
        if (is_array($value)) {
            $this->rules[$key]['error'] = isset($value['error']) ?: null;
            $this->rules[$key]['success'] = isset($value['success']) ?: null;
            $this->rules[$key]['errorHandler'] = isset($value['errorHandler']) ?: null;
            $this->rules[$key]['successHandler'] = isset($value['successHandler']) ?: null;
            return true;
        }
        // $this->properties[$key] = $value;
        return true;
    }

    public function __call($fn, $args)
    {
        if (isset($this->rules[$fn]['function'])) {
            return $this->rules[$fn]['function']($args);
        }
        throw new InvalidException("function $fn dose not defined.");
    }

    public function validate($data, array $rules)
    {
        if ($data && is_array($data)) {
            $parsedRules = $this->pareseRules($rules);
            $checkedResult = $this->checkRules($data, $rules);
            if ($checkedResult['result'] === false) {
                if (!empty($this->$checkedResult['index']['errorHandler']) && \is_callable($this->$checkedResult['index']['errorHandler'])) {
                    call_user_func($this->$checkedResult['index']['errorHandler']);
                }
                if (!empty($this->$checkedResult['index']['error'])) {
                    $this->errors[] = $this->$checkedResult['index']['error'];
                }
            } else {
                if (!empty($this->$checkedResult['index']['successHandler']) && \is_callable($this->$checkedResult['index']['successHandler'])) {
                    call_user_func($this->$checkedResult['index']['successHandler']);
                };
            }
        }
    }

    public function pareseRules($rules)
    {
        foreach ($rules as $filed => $rule) {
            if (strpos($rule, '|') !== false) {
                $rule = explode($rule);
            }
            $rules[$filed] = $rule;
        }
        return $rules;
    }

    protected function checkRules($data, $parsedRules)
    {
        $index = null;
        $dataClone = [];
        $dataClone = $data;
        $result = [];
        foreach ($parsedRules as $key => $rule) {
            $dataClone = $data;
            if (strpos($key, '.') !== false) {
                $keyArr = \explode('.', $key);
                foreach ($keyArr as $keyItem) {
                    $dataClone = $dataClone[$keyItem];
                    $filed = $dataClone;
                }
            } else {
                $filed = $data[$key];
            }
            if (strpos($rule, ':') !== false) {
                $exploder = explode(':', $rule);
                $result['result'] = $this->rules[$exploder[0]]['function']($filed, $exploder[1]);
                $result['index'] = $exploder[0];
                return $result;

            } else {
                $result['result'] = $this->rules[$rule]['function']($filed);
                $result['index'] = $rule;
            }
        }
    }

    public function success()
    {
        return count($this->errors()) === 0 ? true : false;
    }

    public function fails()
    {
        return count($this->errors()) > 0 ? true : false;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function defaultRules()
    {
        $this->required = function ($value) {
            return isset($value) ? true : false;
        };
        $this->max = function ($value, $consult) {
            return is_string($value)
            ? (length($value) <= (int) $consult ? true : false)
            : false;
        };
        $this->test = [
            'function' => function () {

            },
        ];
    }
}
