<?php

namespace App\Admin\Extensions\OrderCount;

use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Encore\Admin\Grid;

class Export extends Field
{
    /**
     * @var string
     */
    private $queryParams;

    /**
     * Create a new CreateButton instance.
     *
     * @param Grid $grid
     * @param string $queryParams
     */
    public function __construct(Grid $grid, string $queryParams)
    {
        $this->grid = $grid;
        $this->queryParams = $queryParams;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render():string
    {

            return <<<EOT
<div class="btn-group pull-right bexport" style="margin-left: 30px">
    <a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="bexport()">
        <i class="fa fa-plus"></i>&nbsp;&nbsp;导出
    </a>
</div>
<script>
    function bexport(){
        var url = new URL(location);
        var pay_time = url.searchParams.get('pay_time[start]');
        if(  pay_time==null || pay_time.length==0){
            alert('请填写支付时间');
        }else{
            window.open("{$this->queryParams}","_blank"); 
        }
    }
</script>
EOT;
    }

}