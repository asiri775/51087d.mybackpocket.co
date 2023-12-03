<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BankAccountController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = new BankAccount();
        $editModel = new BankAccount();
        if(Session::has('editModel')){
            $editModel = Session::get('editModel');
        }
        return view('bank-accounts.index', compact('model', 'editModel'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bankAccount = new BankAccount();
        return $this->save($request, $bankAccount);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param BankAccount $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bankAccount)
    {
        return view('bank-accounts.form', compact('bankAccount'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param BankAccount $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        return $this->save($request, $bankAccount);
    }


    /**
     * responsible to manage update and create
     */
    private function save(Request $request, BankAccount $bankAccount){
        $isNewRecord = true;
        if($bankAccount->id != null){
            $isNewRecord = false;
        }

        $rules = [
            'bank_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if($isNewRecord){
                return redirect()->back()->withErrors($validator)->with('popup', 'newRecordModal')->withInput();
            }else {
                return redirect()->back()->withErrors($validator)->with('popup', 'oldRecordModal')->with('editModel', $bankAccount)->withInput();
            }
        }

        $bankAccount->bank_id = $request->input('bank_id');
        $bankAccount->name = $request->input('name');
        $bankAccount->alias = $request->input('alias');
        $bankAccount->account_number = $request->input('account_number');
        $bankAccount->transit_number = $request->input('transit_number');

        if($isNewRecord){
            $bankAccount->save();
        }else{
            $bankAccount->update();
        }

        return redirect()->route('bank-accounts.index')->with('success','Bank Account saved successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BankAccount $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return redirect()->route('bank-accounts.index')->with('success','Bank Account deleted successfully');
    }
}
