<?php

namespace Handscube\Kernel;

use Handscube\Foundations\BaseModel;
use Handscube\Kernel\Exceptions\InvalidException;

class Model extends BaseModel
{

    /**
     * Default use policy.
     *
     * @param BaseModel $user
     * @param BaseModel $model
     * @return void
     */
    public function defaultPolicy(BaseModel $user, BaseModel $model)
    {
        if ($user->id && $model->id) {
            return $user->id === $model->id;
        }
        throw new InvalidException('One of Model id is invalid.');
    }
}
