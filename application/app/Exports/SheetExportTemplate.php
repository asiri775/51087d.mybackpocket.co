<?php

namespace App\Exports;

use App\Models\TransactionByCategory;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
class SheetExportTemplate  implements FromView, WithTitle
{
    public $from;
    public $to;
    public $year;
    public $category_id;
    public function title(): string
    {

        $catName=Category::where('id', $this->category_id)->first();
  
        return ($catName)?$catName->name:'Uncategorized';
    }
    public function getCategoryTransactions()
    {
        $data=[];
        $category=$this->category_id;
       
            if(isset($this->from) || isset($this->to))
            {  
               $data=TransactionByCategory::where('category_id',$category)->whereBetween('transaction_date', [$this->from, $this->to])->get();
            }
            else {
               $data=TransactionByCategory::where('category_id',$category)->get();
                
            }
       return  $data;
    }


    public function view(): View
    {
        return view('bank-statements.sheetExportTemplate', [
            'transactions' => $this->getCategoryTransactions(),'from'=>$this->from,'to'=>$this->to
        ]);
    }

    public function setFrom($from)
    {
        $this->from=$from;
    }
    public function setTo($to)
    {
        $this->to=$to;
    } 
    
    public function setCategory($category)
    {
        $this->category_id=$category;
    }  

    
}