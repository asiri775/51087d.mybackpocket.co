<?php
namespace App\Http\Controllers\Categories;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class MainCategoryTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $mainCategories = Category::where('mainid', NULL);
    
        if ($request->main_tab_slug != '') {
            $mainCategories = $mainCategories->whereRaw("LOWER(`slug`) like '%".trim(strtolower($request->main_tab_slug))."%'");
        }
        if ($request->main_tab_name != '') {
            $mainCategories = $mainCategories->whereRaw("LOWER(`name`) like '%".trim(strtolower($request->main_tab_name))."%'");
        }
        if ($request->main_tab_type != '') {
            $mainCategories = $mainCategories->where('type', 'like', "%{$request->main_tab_type}%");
        }
        if ($request->main_tab_id != '') {

            $mainCategories = $mainCategories->where('id', 'like', "%{$request->main_tab_id}%");
        } 
        
        $mainCategories->get();
        

        return DataTables::of($mainCategories)
        ->addColumn('checkbox', function ($mainCategory){
            $select = '<input type="checkbox" name="checkbox[]" value="'.$mainCategory->id.'" id="checkbox_'.$mainCategory->id.' checked">';
            return $select;

        })
        ->addColumn('sub', function ($mainCategory){
            $count=Category::where('mainid',$mainCategory->id)->where('role','sub')->count();
            return '<a href="'.route('category.list').'?type=subcat&id='.$mainCategory->id.'"><u>'.$count.'</u></a>';
        })
        ->addColumn('child', function ($mainCategory){
            $subCats=Category::where('mainid',$mainCategory->id)->get();
            $sum=0;
            foreach ($subCats AS $sub) 
            {
                $subCount=Category::where('mainid',$sub->id)->count();
                $sum=$sum+$subCount;
            }
            if($sum)
            {
                return '<a href="'.route('category.list').'?type=subcat&id='.$mainCategory->id.'"><u>'.$sum.'</u></a>';
            } else {
                return '<a href="'.route('category.list').'?type=subcat&id='.$mainCategory->id.'">-</a>';
            }
            
        })
        ->addColumn('actions', function ($mainCategory){
            $action = '
            <div class="btn-group">
            <input type="hidden" name="main_cat_id" id="main_cat_id" value="'. $mainCategory->id .'">
            <a href="' . route('update.category', ['category' => $mainCategory->slug]) . '" class="btn btn-primary" data-toggle="tooltip"
            data-placement="bottom" title="Edit"><i class="fa fa-edit"></i>

            <a href="#!" onclick="deleteCategory(' . $mainCategory->id . ')" type="button" title="Delete" class="btn btn-danger" data-toggle="modal"
            data-target="#delete-main-cat" cat_id="' . $mainCategory->id . '" style="color:#fff;"><i class="fa fa-trash-o"></i></a>
        </div>


               ';
            return $action;
        })
        ->rawColumns(['checkbox','sub','child','actions'])
        ->make(true);
    }
}
