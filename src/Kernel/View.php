<?php

namespace Handscube\Kernel;

use Handscube\Foundations\BaseView;
use Handscube\Kernel\Exceptions\NotFoundException;

/**
 * View Layer [c] Handscube.
 * @author J.W. <email@email.com>
 */

class View extends BaseView
{

    protected $viewFileName;
    protected $defaultViewModuleName = 'home';

    /**
     * Constructor.
     *
     * @param string $view
     */
    public function __construct(string $view)
    {
        parent::__construct();
        $this->__boot($view);
    }

    /**
     * Do some works like find view file position and
     * load view layer.
     *
     * @param string $view
     * @return void
     */
    private function __boot(string $view)
    {
        $this->viewFileName = $this->ensureViewFile($view);
        $this->contents = $this->load($this->viewFileName);
    }

    /**
     * Set deault view module name.
     *
     * @param string $defaultViewModuleName
     * @return void
     */
    public function setDefaultViewModule(string $viewModuleName)
    {
        $this->defaultViewModuleName = $defaultViewModuleName;
    }

    /**
     * Show view;
     *
     * @param [type] $view
     * @return void
     */
    public function show()
    {
        echo $this->contents;
    }

    /**
     * Boot work to find turth view file position.
     *
     * @param [type] $view
     * @return void
     */
    public function ensureViewFile($view)
    {
        if (strpos($view, '.') !== false) {
            $views = explode('.', $view);
            $viewFile = APP_VIEW_PATH . '/' . $views[0] . '/' . ucfirst($views[1]) . '.php';
            if (!file_exists($viewFile)) {
                throw new NotFoundException("View file $viewFile does not exists.");
            }
            return $viewFile;
        } else {
            // ff(APP_VIEW_PATH);
            $viewFile = APP_VIEW_PATH . '/' . $this->defaultViewModuleName . '/' . ucfirst($view) . '.php';
            if (!file_exists($viewFile)) {
                throw new NotFoundException("View file $viewFile does not exists.");
            }
            return $viewFile;
        }
    }

    /**
     * Get contents.
     *
     * @return void
     */
    public function getContents()
    {
        return $this->contents;
    }
}
