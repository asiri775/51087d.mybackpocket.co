<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\TransactionByCategory;
use Illuminate\Support\Facades\DB;
class AllTransactionsExportByYear implements WithMultipleSheets
{
    public $from;
    public $to;
    public $category_id;
    public function sheets(): array
    {
        $list=[];
        $from =$this->from;
        $to =$this->to;
        if(isset($from) || isset($to)){
            $categories=DB::table('transactionsByCategory')->select('category_id')
            ->leftJoin('categories', 'categories.id', '=', 'transactionsByCategory.category_id')
            ->whereBetween('transactionsByCategory.transaction_date', [$from, $to])
            ->where('categories.name','!=','')
            ->groupBy('transactionsByCategory.category_id')->get();
           
        } else {
            $categories=DB::table('transactionsByCategory')->select('category_id')
            ->leftJoin('categories', 'categories.id', '=', 'transactionsByCategory.category_id')
            ->where('categories.name','!=','')
            ->groupBy('category_id')->get();
        }

        foreach($categories as $category)
        {
            if($category)
            {
                $list[]=$category->category_id;
            }
        }

        $sheets=[];
        $i=0;
        if(count($list)>0){
            foreach ($list AS $val)
            {
                    $data= new SheetExportTemplate();
                    $data->setFrom($from);
                    $data->setTo($to);
                    $data->setCategory($val);
                    $sheets['Transactions'.$i] = $data;
                    $i++;
            }
    
        } 
        else {
            $sheets['Not Avaiable'] = [];
        }
      
        return $sheets;
    }

    public function setFrom($from)
    {
        $this->from=$from;
    }
    public function setTo($to)
    {
        $this->to=$to;
    }
    public function setCategory($category_id)
    {
        $this->category_id=$category_id;
    }


}
