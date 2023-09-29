<?php
namespace aspsierra\phpBasicFw\form;

use aspsierra\phpBasicFw\form\BaseField;

class Textarea extends BaseField{
    public function renderInput(): string
    {
        return sprintf(
            '<textarea name="%s" class="form-control %s" placeholder = "s">%s</textarea>',
            $this->model->getLabel($this->attribute),
            $this->model->hasError($this->attribute) ? 'is-invalid' : '',
            $this->attribute,
            $this->model->{$this->attribute},

        );
    }
}