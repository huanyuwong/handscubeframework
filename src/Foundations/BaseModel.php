<?php

namespace Handscube\Foundations;

use App\Kernel\Exceptions\InsideException;
use App\Kernel\Model as BottomModel;
use App\Models\User;

/**
 * Class BaseModel [c] Handsucbe.
 * @author J.W. <email@email.com>
 */

class BaseModel extends \Illuminate\Database\Eloquent\Model
{
    const type = "Model";

    /**
     * Before checking user auth ,you can do some
     * other auth in here.
     *
     * @param User $user
     * @param [type] $model
     * @return void
     */
    public function beforeCan($user, $model)
    {

    }

    /**
     * Checking user authorization.
     * @param string $action
     * @param [stirng | Model] $modelName or $model
     * @return boolean
     */
    public function can(string $action, $model)
    {
        if (in_array($action, BottomModel::EXCEPT_MODEL_POLICY)) {
            return true;
        }
        if ($this->beforeCan($this, $model) === false) {
            return false;
        }
        if (\method_exists($this, $action . "Policy")) {
            if ($this->id) {
                return call_user_func([$this, $action . "Policy"], $this, $model);
            }
            throw new InsideException("Model" . get_called_class() . "must have id");
        }
        return $this->defaultPolicy($this, $model);
        // throw new NotFoundException("Policy $action.Policy does not exists.");
    }

}
