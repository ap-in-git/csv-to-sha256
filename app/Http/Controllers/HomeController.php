<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public static function getCsv($columnNames, $rows, $fileName = 'file.csv')
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $fileName,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $callback = function () use ($columnNames, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnNames);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function index()
    {
        $file = \request()->file("csv")->getRealPath();

        $file = file($file);
        $file_name = \request()->file("csv")->getClientOriginalName();
        $file_name = date("Y-m-d") . "_" . "export_" . $file_name;
        $rows = [];
        foreach ($file as $item) {
            $item = trim($item);
            $item = hash("sha256", $item);
            array_push($rows,[$item]);
        }

        $columnNames = [''];//replace this with your own array of string column headers
        return self::getCsv($columnNames, $rows, $file_name);
    }
}
