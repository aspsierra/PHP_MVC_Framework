<?php

namespace aspsierra\phpBasicFw\core;

use aspsierra\phpBasicFw\core\database\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}
