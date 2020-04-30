<?php

namespace App\Admin\Extensions\Receipt;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Admin;
use Encore\Admin\Form\Field;

//use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

//class Invalid extends RowAction
//{
//    public $name = '复制';
//
//    public function handle(Model $model)
//    {
//        // $model ...
//
//        return $this->response()->success('Success message.')->refresh();
//    }
//}

class Invalid
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {

//        return <<<EOT

//$('.grid-check-row').on('click', function() {
//
//    $.ajax({
//        method: 'post',
//        url: 'receipt/invalid',
//        data: {
//            _token:LA.token,
//            id: $(this).data('id'),
//        },
//        success: function () {
//            $.pjax.reload('#pjax-container');
//            toastr.success('操作成功');
//        }
//    });
//});
//
//EOT;

        return $script = <<<SCRIPT
$('.grid-check-row').click(function() {
var id = $(this).data('id');
    swal({
        title: "确认作废",
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
                    url: 'receipt/invalid',
                    data: {
                        method:'post',
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

        return "<a class='btn btn-xs btn-danger grid-check-row' data-id='{$this->id}' style='margin-left:12px;color:white'>作废</a>";
    }

    public function __toString()
    {
        return $this->render();
    }

}