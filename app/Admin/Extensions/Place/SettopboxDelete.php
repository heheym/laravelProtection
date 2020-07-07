<?php

namespace App\Admin\Extensions\Place;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Admin;
use Encore\Admin\Form\Field;

//use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;


class SettopboxDelete
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {

        return $script = <<<SCRIPT
$('.settopbox-grid-row-delete').click(function() {
var id = $(this).data('id');
    swal({
        title: "确认删除",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确定",
        showLoaderOnConfirm: true,
        cancelButtonText: "取消",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: 'settopbox/'+id,
                    data: {
                        _method:'delete',
                        id: id,
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');
                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});

SCRIPT;
    }

    public function render()
    {
        Admin::script($this->script());

        return "<a href='javascript:void(0)' class='settopbox-grid-row-delete' data-id='{$this->id}' style='padding-left:10px;'>
        <i class='fa fa-trash'></i></a>";

        return "<a class='btn btn-xs btn-danger grid-check-row' data-id='{$this->id}' style='margin-left:12px;color:white'>作废</a>";

        return "<a href=settopbox/$this->id/edit class='settopbox-grid-row-edit' style='padding-left:10px'>
    <i class='fa fa-edit'></i>
</a>";

    }

    public function __toString()
    {
        return $this->render();
    }

}