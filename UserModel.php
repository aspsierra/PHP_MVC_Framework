<?php

namespace aspfw\app\core;

use aspfw\app\core\database\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}
