<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
class AllTransactionsExport implements WithMultipleSheets
{
    public $from;
    public $to;
    public function sheets(): array
    {

        $first= new FirstSheetImport();
        $first->setFrom($this->from);
        $first->setTo($this->to);
        $second= new SecondSheetImport();
        $second->setFrom($this->from);
        $second->setTo($this->to);
        return [
            'Transactions Report Summary' => $first,
            'Transactions Report Level2' => $second,
        ];
    }

    public function setFrom($from)
    {
        $this->from=$from;
    }
    public function setTo($to)
    {
        $this->to=$to;
    }


}
