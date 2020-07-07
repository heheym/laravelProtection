<?php

namespace App\Admin\Extensions\Place;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Admin;
use Encore\Admin\Form\Field;

use Illuminate\Database\Eloquent\Model;

class SettopboxEdit
{
//    public $name = '编辑';

    /**
     * @return string
     */
//    public function href()
//    {
//        return "123";
//    }

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function render()
    {
//        Admin::script($this->script());

        return "<a href=settopbox/$this->id/edit class='settopbox-grid-row-edit' style='padding-left:10px'>
    <i class='fa fa-edit'></i>
</a>";



    }

    public function __toString()
    {
        return $this->render();
    }
}