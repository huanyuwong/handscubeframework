<?php

namespace Handscube\Foundations;

class BaseView
{
    public $viewModuleName = 'home';
    public $vars = [];
    public static $parseRules = [];
    protected $contents;

    const __RULES__ = [
        '/\{\s*\$(\w*?)\s*\}/',
    ];

    public function __construct()
    {
        $this->__boot();
    }

    private function __boot()
    {
        $this->registerParseRules();
    }

    /**
     * Loop compilation variable
     *
     * @param [type] $contents [view contents]
     * @return string [complied contents]
     */
    public function compile($contents)
    {
        foreach (self::$parseRules as $pattern) {
            preg_match_all($pattern, $contents, $match);
            $contents = \preg_replace_callback(
                $pattern,
                function ($match) {
                    return $this->vars[$match[1]];
                },
                $contents
            );
            // $contents = \preg_filter($pattern, $this->vars[('${1}')], $contents);
        }
        $this->contents = $contents;
        return $contents;
    }

    /**
     * Register regex rules.
     *
     * @return void
     */
    public function registerParseRules()
    {
        foreach (self::__RULES__ as $rule) {
            $this->addOneRule($rule);
        }
    }

    /**
     * Register view layer paramsters.
     *
     * @param array $properties
     * @return void
     */
    public function registerProperties(array $properties)
    {
        foreach ($properties as $key => $property) {
            $this->vars[$key] = $property;
        }
    }

    /**
     * Add one regex rule.
     *
     * @param string $rule
     * @return void
     */
    public function addOneRule(string $rule)
    {
        self::$parseRules[] = $rule;
    }

    /**
     * Return view layer with parameters.
     *
     * @param array $properties
     * @return void
     */
    public function with(array $properties)
    {
        $this->registerProperties($properties);
        $this->compile($this->contents);
        return $this;
    }

    /**
     * Load view.
     *
     * @param [type] $viewName
     * @return void
     */
    public function load($viewName)
    {
        return file_get_contents($viewName);
    }

}
