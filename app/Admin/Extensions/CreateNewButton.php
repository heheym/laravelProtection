<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Encore\Admin\Grid;

class CreateNewButton extends Field
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
        $new = trans('admin.new');
        return <<<EOT

<div class="btn-group pull-right" style="margin-left: 30px">
    <a href="{$this->grid->resource()}/create{$this->queryParams}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>&nbsp;&nbsp;{$new}
    </a>
</div>

EOT;
    }
}