<?php

namespace Handscube\Foundations;

use App\Kernel\Exceptions\InsideException;
use App\Kernel\Exceptions\NotFoundException;
use App\Models\User;

class BaseModel extends \Illuminate\Database\Eloquent\Model
{
    const type = "Model";
    const POLICY_WITHOUT_MODEL = [
        "create",
    ];

    public function beforeCan(User $user, $model)
    {

    }

    /**
     * Checking user authorization
     * @param string $action
     * @param [stirng | Model] $modelName or $model
     * @return boolean
     */
    public function can(string $action, $model)
    {
        if ($this->beforeCan($this, $model) === false) {
            return false;
        }
        if (\method_exists($this, $action . "Policy")) {
            if ($this->id) {
                return call_user_func([$this, $action . "Policy"], $this, $model);
            }
            throw new InsideException("Model" . get_called_class() . "must have id");
        }
        throw new NotFoundException("Policy $action.Policy does not exists.");
    }

}
