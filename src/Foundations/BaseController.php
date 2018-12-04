<?php

namespace Handscube\Foundations;

use Handscube\Kernel\Component;
use Handscube\Kernel\Events\Event;
use Handscube\Kernel\Route;
use Handscube\Traits\DispatchTrait;

class BaseController extends Component
{
    use DispatchTrait;

    public function __construct()
    {
    }

    /**
     * Controller Model bind method.
     *
     * @return void
     */
    public static function model()
    {

    }

    /**
     * Return an object instance of Response.
     *
     * @param string $content
     * @param integer $status
     * @param array $headers
     * @return void
     */
    public function response($content = 'null', $status = 200, $headers = [])
    {
        return new \Handscube\Kernel\Response($content, $status, $headers);
    }

    /**
     * Redirect a route or a url.
     *
     * @param string $target [url | route name]
     * @param array $params
     * @return void
     */
    public function redirect(string $target, array $params = [])
    {
        if (!$target) {
            return $this->back();
        }
        Route::redirect($target, $params);
    }

    /**
     * Redirect back.
     *
     * @return void
     */
    public function back()
    {
        if (!$this->app->request->header['Referer']) {
            return;
        }
        Route::redirect($this->app->request->header['Referer']);
    }

    /**
     * Return current request.
     *
     * @return void
     */
    public function request()
    {
        return $this->app->request;
    }

    /**
     * Trigger an event.
     *
     * @param [BaseEvent] $event
     * @param [Object] $trigger
     * @return void
     */
    public function emit($event, $trigger)
    {
        Event::emit($event, $trigger);
    }

    /**
     * Alias of @method emit.
     *
     * @param [type] $event
     * @param [type] $trigger
     * @return void
     */
    public function trigger($event, $trigger)
    {
        $this->emit($event, $trigger);
    }

    /**
     * Alias of @method emit.
     *
     * @param [type] $event
     * @param [type] $trigger
     * @return void
     */
    public function fire($event, $trigger)
    {
        $this->emit($event, $trigger);
    }

}
