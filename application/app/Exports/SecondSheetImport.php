<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
class SecondSheetImport  implements FromView, WithTitle
{
    public $from;
    public $to;
    public function title(): string
    {
        return 'Transactions Report Level2';
    }

    public function getChildCategoryTransactions()
    {
        $list=[];
        $data=[];
        $from =$this->from;
        $to =$this->to;
        $fromDate=explode('/', $from);
        $first= Transaction::orderBy('id', 'ASC')->first();

        if(isset($fromDate[2]))
        {
            $from=$fromDate[2].'-'.$fromDate[0].'-'.$fromDate[1];
        }
        else {
            $from=$first->transaction_date;
        }

        $fromTo=explode('/', $to);
        if(isset($fromTo[2]))
        {
            $to=$fromTo[2].'-'.$fromTo[0].'-'.$fromTo[1];
        } else {
            $to=date('Y-m-d');
        }

        $subCategorires=Category::where('type', 'accounting')->where('role', 'sub')->orderBy('id', 'ASC')->get();

        foreach($subCategorires AS $sub)
        {

            $subTotal=0;
            $childtotal=0;
            if(isset($from) || isset($to)){
                $SubTrasactionsDebit=Transaction::where('is_debit',1)->where('category_id',$sub->id)->whereBetween('transaction_date', [$from, $to])->get();
                $SubTrasactionsCredit=Transaction::where('is_debit',0)->where('category_id',$sub->id)->whereBetween('transaction_date', [$from, $to])->get();
            }
            else {
                $SubTrasactionsDebit=Transaction::where('is_debit',1)->where('category_id',$sub->id)->get();
                $SubTrasactionsCredit=Transaction::where('is_debit',0)->where('category_id',$sub->id)->get();
            }

            $childCategorires=Category::where('mainid', $sub->id)->orderBy('id', 'ASC')->get();
            $data=[];
            foreach($childCategorires AS $child)
            {

                if (isset($from) || isset($to)) {
                    $childTrasactionsDebit=Transaction::where('is_debit',1)->where('category_id',$child->id)->whereBetween('transaction_date', [$from, $to])->get();
                    $childTrasactionsCredit=Transaction::where('is_debit',0)->where('category_id',$child->id)->whereBetween('transaction_date', [$from, $to])->get();
                }
                else {
                    $childTrasactionsDebit=Transaction::where('is_debit',1)->where('category_id',$child->id)->get();
                    $childTrasactionsCredit=Transaction::where('is_debit',0)->where('category_id',$child->id)->get();
                }

                $childtotalD=0;
                $childtotalC=0;
                $childQtyD=0;
                $childQtyC=0;
                foreach($childTrasactionsDebit AS $childTrasD)
                {
                    if(isset($childTrasD->total)){
                        $childtotalD=$childtotalD+$childTrasD->total;
                        $childQtyD=$childQtyD+1;
                    }
                }
                foreach($childTrasactionsCredit AS $childTrasC)
                {
                    if(isset($childTrasC->total)){
                        $childtotalC=$childtotalC+$childTrasC->total;
                        $childQtyC= $childQtyC+1;
                    }
                }
                $childtotal=$childtotalD-$childtotalC;
                $childQty=$childQtyD+$childQtyC;
                $data[]=['child_name'=>$child->name,'childtotal'=>$childtotal,'childQty'=>$childQty];
            }
            $subTotalD=0;
            $subTotalC=0;
            $subQtyD=0;
            $subQtyC=0;
            foreach($SubTrasactionsDebit AS $subTrasactionD)
            {
                if(isset($subTrasactionD->total)){
                    $subTotalD=$subTotalD+$subTrasactionD->total;
                    $subQtyD=$subQtyD+1;
                }
            }
            foreach($SubTrasactionsCredit AS $subTrasactionC)
            {
                if(isset($subTrasactionC->total)){
                    $subTotalC=$subTotalC+$subTrasactionC->total;
                    $subQtyC=$subQtyC+1;
                }
            }

            $subTotal=$subTotalD-$subTotalC;
            $subQty=$subQtyD+$subQtyC;
            if(isset($sub->name)){
                $list[]=['sub_name'=>$sub->name,'data'=>$data,'subTotal'=>$subTotal,'subQty'=>$subQty];
            }
        }
       return  $list;
    }
    public function view(): View
    {
        return view('bank-statements.secondTransactionsExport', [
            'transactions' => $this->getChildCategoryTransactions(),'from'=>$this->from,'to'=>$this->to
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
}
