<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
//            ->header('Dashboard')
//            ->description('Description...')
//            ->row(Dashboard::title())
            ->row(function (Row $row) {


                $row->column(4, function (Column $column) {
                    $externalContent = file_get_contents('http://checkip.dyndns.com/');
                    preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
                    $externalIp = $m[1];
                    $column->append($externalIp);
                });

//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::extensions());
//                });

//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::dependencies());
//                });
            });
    }
}
