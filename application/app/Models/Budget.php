<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\User;

class Budget extends Model
{
    protected $table = "budgets";


    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'id');
    }


    public function getCategoryName($cat_id)
    {
        $categoryName = Category::select('name')->where('id', $cat_id)->first();
        return $categoryName;
    }

    public function getUserById($id)
    {

        $userName = User::select('name')->where('id', $id)->first();
        return $userName;
    }

    public function BudgetAmount($id)
    {


        $transactions = Transaction::where('budget_id', $id)->get();

        if ($transactions) {
            $amount =   0.00;
            foreach ($transactions as $transaction) {
                $total  =   $transaction->total;
                $amount +=   $total;
            }
        } else {
            $amount = 'No Receipts';
        }

        return $amount;
    }

    public function getBudgetAmountById($ids)
    {
        $transactions = Transaction::where('budget_id', $ids)->get();
        if ($transactions) {
            $amount =   0.00;
            foreach ($transactions as $transaction) {
                $total  =   $transaction->total;
                $amount +=   $total;
            }
        } else {
            $amount = 'No Receipts';
        }
        return  number_format((float)($amount), 2, '.', ',');
    }

    public static function getGrandBudgetTotal($userId)
    {
        $total = 0.00;
        $budgets = Budget::where('created_by', $userId)->get();
        foreach ($budgets as $budget) {
            $total  =   $total + $budget->getBudgetAmountById($budget->id);
        }


        return number_format((float)($total), 2, '.', ',');
    }
}

