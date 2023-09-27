<?php

namespace aspsierra\phpBasicFw;

use aspsierra\phpBasicFw\database\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}
