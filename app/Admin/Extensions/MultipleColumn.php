<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form;
use Encore\Admin\Form\Field;

class MultipleColumn extends Field
{
    protected $label;

    /**
     * Callback for add field to current row.s.
     *
     * @var \Closure
     */
    protected $callback;

    /**
     * Parent form.
     *
     * @var Form
     */
    protected $form;

    /**
     * Fields in this row.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Default field width for appended field.
     *
     * @var int
     */
    protected $defaultFieldWidth = 12;

    protected $view = 'form.multiplecolumn';

    public function __construct($label, $arguments = [])
    {
        $this->label = $label;
        $this->callback = $arguments[0];
        $this->form = $arguments[1];
        call_user_func($this->callback, $this);
    }

    /**
     * Set width for a incomming field.
     *
     * @param int $width
     *
     * @return $this
     */
    public function width($width = 12)
    {
        $this->defaultFieldWidth = $width;

        return $this;
    }

    public function render()
    {
        return parent::render($this->view)->with([
            'fields' => $this->fields,
            'label' => $this->label,
        ]);

    }

    /**
     * Add field.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field|void
     */
    public function __call($method, $arguments)
    {
        $field = $this->form->__call($method, $arguments);

        $this->fields[] = [
            'width'   => $this->defaultFieldWidth,
            'element' => $field,
        ];

        return $field;
    }
}