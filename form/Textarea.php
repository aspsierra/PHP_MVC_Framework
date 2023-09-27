<?php
namespace aspfw\app\core\form;

use aspfw\app\core\form\BaseField;

class Textarea extends BaseField{
    public function renderInput(): string
    {
        return sprintf(
            '<textarea name="%s" class="form-control %s">%s</textarea>',
            $this->attribute,
            $this->model->hasError($this->attribute) ? 'is-invalid' : '',
            $this->model->{$this->attribute},

        );
    }
}