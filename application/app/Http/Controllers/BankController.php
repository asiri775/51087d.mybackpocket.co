<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BankController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = new Bank();
        $editModel = new Bank();
        if(Session::has('editModel')){
            $editModel = Session::get('editModel');
        }
        return view('banks.index', compact('model', 'editModel'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bank = new Bank();
        return $this->save($request, $bank);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        return view('banks.form', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        return $this->save($request, $bank);
    }


    /**
     * responsible to manage update and create
     */
    private function save(Request $request, Bank $bank){
        $isNewRecord = true;
        if($bank->id != null){
            $isNewRecord = false;
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if($isNewRecord){
                return redirect()->back()->withErrors($validator)->with('popup', 'newRecordModal')->withInput();
            }else {
                return redirect()->back()->withErrors($validator)->with('popup', 'oldRecordModal')->with('editModel', $bank)->withInput();
            }
        }

        $bank->name = $request->input('name');
        $bank->code = $request->input('code');
        $bank->swift_code = $request->input('swift_code');

        if($isNewRecord){
            $bank->save();
        }else{
            $bank->update();
        }

        return redirect()->route('banks.index')->with('success','Bank saved successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('banks.index')->with('success','Bank deleted successfully');
    }
}
