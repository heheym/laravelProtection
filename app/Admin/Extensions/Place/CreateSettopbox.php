<?php

namespace App\Admin\Extensions\Place;

use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Encore\Admin\Grid;

class CreateSettopbox extends Field
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
        $this->queryParams = $queryParams;
        $this->grid = $grid;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render():string
    {
        parse_str($this->queryParams, $arr);

        if (!array_key_exists("settopbox_key",$arr)){
            return <<<EOT
<div class="btn-group pull-right" style="margin-left: 30px">
    <a href="javascript:void(0)" onclick="alert('请先选中某个场所')" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>&nbsp;&nbsp;新增
    </a>
</div>
EOT;
        }
        return <<<EOT

<div class="btn-group pull-right" style="margin-left: 30px">
    <a href="settopbox/create?{$this->queryParams}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>&nbsp;&nbsp;新增
    </a>
</div>

EOT;
    }

}