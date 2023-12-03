<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Yajra\DataTables\DataTables;
use App\User;
use Illuminate\Http\Request;

class UserTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $users = User::where('is_active',1)->orderBy('id', 'desc')->get();
        // switch (request()->date_option) {
        //     case 'today':
        //         $transactions = $transactions->whereDate('transaction_date', '=', Carbon::now());
        //         break;
        //     case 'this_whole_week':
        //         $start = Carbon::now()->startOfWeek();
        //         $end = Carbon::now()->endOfWeek();
        //         $transactions = $transactions->whereBetween('transaction_date', [$start, $end]);
        //         break;
        //     case 'this_month':
        //         $start = Carbon::now()->startOfMonth();
        //         $end = Carbon::now()->endOfMonth();
        //         $transactions = $transactions->whereBetween('transaction_date', [$start, $end]);
        //         break;
        //     case 'this_year':
        //         $start = Carbon::now()->startOfYear();
        //         $end = Carbon::now()->endOfYear();
        //         $transactions = $transactions->whereBetween('transaction_date', [$start, $end]);
        //         break;
        //     default:
        //         break;
        // }
        // if (request()->from) {
        //     $transactions = $transactions->whereDate('transaction_date', '>=', Carbon::createFromDate(request()->from));
        // }
        // if(request()->to) {
        //     $transactions = $transactions->whereDate('transaction_date', '<=', Carbon::createFromDate(request()->to));
        // }
        // if (request()->vendor_email) {
        //     $email = $request->vendor_email;
        //     $transactions = $transactions->whereHas('vendor', function ($query) use ($email) {
        //         $query->where('email', 'like', "%{$email}%");
        //     });
        // }
        // if ($request->vendor_id != '') {
        //     $transactions->where('vendor_id', "{$request->vendor_id}");
        // }
        // $transactions->orderBy('transaction_date', 'DESC');
        // if ($request->order_no != '') {
        //     $transactions = $transactions->where('order_no', 'like', "%{$request->order_no}%")->get();
        // } else {
        //     $transactions = $transactions->get();
        // }
        return DataTables::of($users)
            ->addColumn('checkboxes', function ($user) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $user->id . '" />';
                return $action;
            })

            ->addColumn('role', function ($user) {
                return $user->getRoleName($user->role_id);
            })
            ->addColumn('actions', function ($user) {
                $action = '
                <div class="btn-group">
                    <a href="' . route('admin.users.edit', ['user' => $user->id]) . '" class="btn btn-complete" data-toggle="tooltip"
                    data-placement="bottom" title="Edit"><i class="fa fa-edit"></i>
                    </a>
                    <a href="' . route('admin.users.show', ['user' => $user->id]) . '" class="btn btn-primary" data-toggle="tooltip"
                    data-placement="bottom" title="Show"><i class="fa fa-eye"></i>
                    </a>

                    <a href="#!"  data-placement="top" title="Delete" data-toggle="modal" data-url="'.route('admin.users.delete', ['user' => $user->id]).'" data-target="#deleteModal"
                    class="btn btn-danger delete"> <i class="fa fa-trash-o"></i></a>

                </div>
            ';
                return $action; //$transaction->action_buttons;
            })
            ->rawColumns(['checkboxes', 'actions'])
            ->make(true);
    }
}
