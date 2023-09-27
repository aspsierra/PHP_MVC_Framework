<?php

namespace aspsierra\phpBasicFw\form;

use aspsierra\phpBasicFw\Model;

abstract class BaseField{

    public Model $model;
    public mixed $attribute;

    public function __construct(Model $model, mixed $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }
    abstract public function renderInput() : string;

    public function __toString()
    {
        return sprintf(
            '
            <div class="form-group">
                <label>%s</label>
                %s
                <div class="invalid-feedback">
                    %s
                </div>
            </div>',
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }

}