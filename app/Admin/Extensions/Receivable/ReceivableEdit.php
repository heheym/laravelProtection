<?php

namespace App\Admin\Extensions\Receivable;

use Encore\Admin\Actions\RowAction;

class ReceivableEdit extends RowAction
{
    public $name = '查看评论';

    /**
     * @return string
     */
    public function href()
    {
        return "/your/uri/path";
    }
}