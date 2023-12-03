<?php
namespace App\Http\Controllers\Categories;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;

class ChildCategoryTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $childCategories = Category::where('role', "child");

        if ($request->main_tab_slug != '') {
            $childCategories = $childCategories->whereRaw("LOWER(`slug`) like '%".trim(strtolower($request->main_tab_slug))."%'");
        }
        if ($request->main_tab_name != '') {
            $childCategories = $childCategories->whereRaw("LOWER(`name`) like '%".trim(strtolower($request->main_tab_name))."%'");
        }
        if ($request->main_tab_type != '') {
            $childCategories = $childCategories->where('type', 'like', "%{$request->main_tab_type}%");
        }
        if ($request->main_tab_id != '') {

            $childCategories = $childCategories->where('id', 'like', "%{$request->main_tab_id}%");
        }
        if ($request->main_tab_category != '') {

            $childCategories = $childCategories->where('mainid', 'like', "%{$request->main_tab_category}%");
        } 
        
        $childCategories->get();
        
        return DataTables::of($childCategories)
        ->addColumn('checkbox', function ($childCategory){
            $select = '<input type="checkbox" name="checkbox[]" value="'.$childCategory->id.'" id="checkbox_'.$childCategory->id.' checked">';
            return $select;

        })
        ->addColumn('main_category', function ($childCategory){
            $subCat = $childCategory->main($childCategory->mainid);
            return $subCat;
        })
        ->addColumn('actions', function ($childCategory){
            $action = '
            <div class="btn-group">
            <a href="' . route('update.category', ['category' => $childCategory->slug]) . '" class="btn btn-primary" data-toggle="tooltip"
            data-placement="bottom" title="Edit"><i class="fa fa-edit"></i>
        <a href="#!" onclick="deleteCategory(' . $childCategory->id . ')" type="button" title="Delete" class="btn btn-danger" data-toggle="modal"
        data-target="#delete-main-cat" cat_id="' . $childCategory->id . '" style="color:#fff;"><i class="fa fa-trash-o"></i></a>
        </div>
                ';
            return $action;
        })
        ->rawColumns(['checkbox', 'actions'])
        ->make(true);
    }
}
