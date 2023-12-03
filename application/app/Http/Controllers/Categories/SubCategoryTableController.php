<?php
namespace App\Http\Controllers\Categories;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Yajra\DataTables\DataTables;


use Illuminate\Http\Request;

class SubCategoryTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $subCategories = Category::where('role', 'sub');
        if ($request->main_tab_slug != '') {
            $subCategories = $subCategories->whereRaw("LOWER(`slug`) like '%".trim(strtolower($request->main_tab_slug))."%'");
        }
        if ($request->main_tab_name != '') {
            $subCategories = $subCategories->whereRaw("LOWER(`name`) like '%".trim(strtolower($request->main_tab_name))."%'");
        }
        if ($request->main_tab_type != '') {
            $subCategories = $subCategories->where('type', 'like', "%{$request->main_tab_type}%");
        }
        if ($request->main_tab_id != '') {

            $subCategories = $subCategories->where('id', 'like', "%{$request->main_tab_id}%");
        } 
        if ($request->main_tab_category != '') {

            $subCategories = $subCategories->where('mainid', 'like', "%{$request->main_tab_category}%");
        } 

        $subCategories->get();

        return DataTables::of($subCategories)
        ->addColumn('checkbox', function ($subCategory){
            $select = '<input type="checkbox" name="checkbox[]" value="'.$subCategory->id.'" id="checkbox_'.$subCategory->id.' checked">';
            return $select;

        })
        ->addColumn('main_category', function ($subCategory){
            $mainCat = $subCategory->main($subCategory->mainid);
            return $mainCat;
        })
        ->addColumn('actions', function ($subCategory){
            $action = '
            <div class="btn-group">
            <a href="' . route('update.category', ['category' => $subCategory->slug]) . '" class="btn btn-primary" data-toggle="tooltip"
            data-placement="bottom" title="Edit"><i class="fa fa-edit"></i>
        <a href="#!" onclick="deleteCategory(' . $subCategory->id . ')" type="button" title="Delete" class="btn btn-danger" data-toggle="modal"
        data-target="#delete-main-cat" cat_id="' . $subCategory->id . '" style="color:#fff;"><i class="fa fa-trash-o"></i></a>
        </div>
                ';
            return $action;
        })
        ->rawColumns(['checkbox', 'actions'])
        ->make(true);
    }
}
