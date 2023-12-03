<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class RemoveController extends Controller
{
    public function RemoveCharacters()
    {
        $traansactions = Transaction::all();
        $specChars = array(
            '-' => '',
            'CA$' => '',
            'CAD' => '',
            '$' => '',
            'CAD ' => ''

        );

        foreach ($traansactions as $transaction) {
            $total = $transaction->total;
            $subTotal = $transaction->sub_total;
            $tax_amount = $transaction->tax_amount;
            foreach ($specChars as $k => $v) {
                $transaction->total = str_replace($k, $v, $total);
                $transaction->sub_total = str_replace($k, $v, $subTotal);
                $transaction->tax_amount = str_replace($k, $v, $tax_amount);
                $transaction->update();
            }
            echo '<pre>' ;
            print_r($transaction->tax_amount) ;

            // $transaction->update();
        }

        die();


    }
}
