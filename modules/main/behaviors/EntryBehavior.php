<?php

namespace modules\main\behaviors;

use modules\main\MainModule;
use yii\base\Behavior;

class EntryBehavior extends Behavior
{
    public function ping()
    {
        return 'Pong';
    }
}