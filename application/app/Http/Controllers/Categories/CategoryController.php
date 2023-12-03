<?php

namespace App\Http\Controllers\Categories;

use App\Models\Category;
use App\Models\Envelope;
use App\Models\Budget;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Session::forget('category_id_session');
        $mainCategories = Category::where('role',  "main")->orderBy('id', 'DESC')->get();
        $subCategories = Category::where('role',  "sub")->orderBy('id', 'DESC')->get();
        $childCategories = Category::where('role', "child")->orderBy('id', 'DESC')->get();
        return view('admin.categories.index', compact('mainCategories', 'subCategories', 'childCategories'));
    }

    // Add main category
    public function addMainCategory(Request $request)
    {
        $check=Category::whereRaw("role='main' AND LOWER(`name`) LIKE '".trim(strtolower($request->main_cat_name))."' 
        AND LOWER(`slug`) LIKE '".trim(strtolower($request->main_slug))."' AND type='".$request->category_type."'")->count();
        if($check)
        {
            Session::flash('error', 'Your category - ' . $request->main_cat_name.' already exist');
        }
        else {
            $mainCategory   =   new Category();
            $mainCategory->role   =  "main";
            $mainCategory->name   =  $request->main_cat_name;
            $mainCategory->slug   =  $request->main_slug;
            $mainCategory->type =  $request->category_type;
            $mainCategory->save();
            Session::flash('success', 'You have successfully added the category - ' . $mainCategory->name);
        }
 
        return redirect(route('category.list').'?type=main');
    }

     // Add sub category
    public function addSubCategory(Request $request)
    {
        $check=Category::whereRaw("role='sub' AND LOWER(`name`) LIKE '".trim(strtolower($request->sub_cat_name))."' 
        AND LOWER(`slug`) LIKE '".trim(strtolower($request->sub_cat_slug))."' AND type='".$request->category_type."'")->count();
        if($check)
        {
            Session::flash('error', 'Your category - ' . $request->main_cat_name.' already exist');
        }
        else {
            $type=Category::where('id',$request->main_category)->first();
            $subCategory   =   new Category();
            $subCategory->role   =  "sub";
            $subCategory->mainid   =  $request->main_category;
            $subCategory->name   =  $request->sub_cat_name;
            $subCategory->slug   =  $request->sub_cat_slug;
            $subCategory->type =  $type->type;
            $subCategory->save();
            Session::flash('success', 'You have successfully added the category - ' . $subCategory->name);
        }
        return redirect(route('category.list').'?type=subcat');
    }
    
     // Add child category
    public function addChildCategory(Request $request)
    {
        $check=Category::whereRaw("role='child' AND LOWER(`name`) LIKE '".trim(strtolower($request->child_cat_name))."' 
        AND LOWER(`slug`) LIKE '".trim(strtolower($request->child_cat_slug))."' AND type='".$request->category_type."'")->count();
        if($check)
        {
            Session::flash('error', 'Your category - ' . $request->main_cat_name.' already exist');
        }
        else {
            $type=Category::where('id',$request->sub_category)->first();
            $childCategory   =   new Category();
            $childCategory->role   =  "child";
            $childCategory->mainid   =  $request->sub_category;
            $childCategory->name   =  $request->child_cat_name;
            $childCategory->slug   =  $request->child_cat_slug;
            $childCategory->type =  $type->type;
            $childCategory->save();
            Session::flash('success', 'You have successfully added the category - ' . $childCategory->name);
        }
        return redirect(route('category.list').'?type=childcat');
    }

    public function updateCategory($slug)
    {
        $category = Category::where('slug', $slug)->first();
        $mainCategories = Category::where('role',  "main")->orderBy('id', 'DESC')->get();
        return view('admin.categories.edit', compact('category', 'mainCategories'));
    }
    public function updateCategoryPost(Request $request, $id)
    {
        $category = Category::where('id', $id)->first();

        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->type =  $request->category_type;
        if($category->role != "main")
        {
            $category->mainid = $request->main_category;
        }
        $category->update();

        Session::flash('success', 'You have successfully updated the category - ' . $category->name);
        if($category->role=='main'){
            return redirect(route('category.list').'?type=main');
        }
        else if($category->role=='sub'){
            return redirect(route('category.list').'?type=subcat');
        }
        else if($category->role=='child'){
            return redirect(route('category.list').'?type=childcat');
        }
       
    }

    public function destroyCat($id) {
        $category = Category::find($id);
        $envelopes = Envelope::where(['category_id' => $id])->get();
        $budgets = Budget::where(['category_id' => $id])->get();

        $sub_cats = Category::where('mainid', $id)->get();



        if (count($envelopes) > 0) {
            Session::flash('success', 'You can\'t remove category - ' . $category->name);
            return redirect()->back();
        }

        if (count($budgets) > 0) {
            Session::flash('success', 'You can\'t remove category - ' . $category->name);
            return redirect()->back();
        }

        if ($category->role == "main") {
            $subs = Category::where('mainid', $category->id)->get();

            foreach ($subs as $sub) {
                $childs = Category::where('mainid', $sub->id)->get();
                foreach ($childs as $child) {
                    $child->delete();
                }

                $sub->delete();
            }
            $category->delete();
        } else if ($category->role == "sub") {
            $childs = Category::where('mainid', $category->id)->get();
            foreach ($childs as $child) {
                $child->delete();
            }
            $category->delete();
        } else {
            $category->delete();
        }
    }

    public function deleteCategory(Request $request)
    {
        $this->destroyCat($request->cat_id);
        Session::flash('success', 'You have successfully removed the category ');
        return redirect()->back();
    }

    public function deleteCategories(Request $request)
    {
        $catIds = Session::get('category_id_session');
        if (isset($catIds)) {
            foreach ($catIds as $catId) {
                $category = Category::where('id', $catId)->first();

                $envelopes = Envelope::where(['category_id' => $category->id])->get();
                $budgets = Budget::where(['category_id' => $category->id])->get();

                if (count($envelopes) > 0) {
                    Session::flash('success', 'You can\'t remove category - ' . $category->name);
                    return redirect()->back();
                }

                if (count($budgets) > 0) {
                    Session::flash('success', 'You can\'t remove category - ' . $category->name);
                    return redirect()->back();
                }

                if ($category->role == "main") {
                    $subs = Category::where('mainid', $category->id)->get();

                    foreach ($subs as $sub) {
                        $childs = Category::where('mainid', $sub->id)->get();
                        foreach ($childs as $child) {
                            $child->delete();
                        }

                        $sub->delete();
                    }
                    $category->delete();
                } else if ($category->role == "sub") {
                    $childs = Category::where('mainid', $category->id)->get();
                    foreach ($childs as $child) {
                        $child->delete();
                    }
                    $category->delete();
                } else {
                    $category->delete();
                }
            }
            Session::forget('category_id_session');
            Session::flash('success', 'You have successfully removed the categories ');
            return redirect()->back();
        } else {
            Session::flash('error', 'You have not selected cetegoires');
            return redirect()->back();
        }
    }
    public function editMainCategory(Request $request)
    {

        $mainCat = Category::where('id', $request->main_id)->first();

        $envelopes = Envelope::where(['category_id' => $request->main_id])->get();
        $budgets = Budget::where(['category_id' => $request->main_id])->get();

        $subCats = Category::where('mainid', $request->main_id)->get();
        if (count($subCats) > 0) {
            Session::flash('success', 'You can\'t edit this category - ' . $mainCat->name);
            return redirect()->back();
        }
        if (count($envelopes) > 0) {
            Session::flash('success', 'You can\'t edit this category - ' . $mainCat->name);
            return redirect()->back();
        }

        if (count($budgets) > 0) {
            Session::flash('success', 'You can\'t edit this category - ' . $mainCat->name);
            return redirect()->back();
        }

        $mainCat->name = $request->edit_main_cat_name;
        $mainCat->slug = $request->edit_main_cat_slug;
        $mainCat->update();

        Session::flash('success', 'You have successfully updated the category - ' . $mainCat->name);
        return redirect()->back();
    }

    public function editSubCategory(Request $request)
    {


        $subCat = Category::where('id', $request->sub_id)->first();

        $envelopes = Envelope::where(['category_id' => $request->sub_id])->get();
        $budgets = Budget::where(['category_id' => $request->sub_id])->get();

        if (count($envelopes) > 0) {
            Session::flash('success', 'You can\'t edit category - ' . $subCat->name);
            return redirect()->back();
        }

        if (count($budgets) > 0) {
            Session::flash('success', 'You can\'t edit category - ' . $subCat->name);
            return redirect()->back();
        }
        $subCat->name = $request->edit_sub_cat_name;
        $subCat->slug = $request->edit_sub_cat_slug;
        if ($request->edit_sub_main_cat != 0) {
            $subCat->mainid = $request->edit_sub_main_cat;
        }
        $subCat->update();

        Session::flash('success', 'You have successfully updated the category - ' . $subCat->name);
        return redirect()->back();
    }
    public function editChildCategory(Request $request)
    {


        $childCat = Category::where('id', $request->child_id)->first();

        $envelopes = Envelope::where(['category_id' => $request->child_id])->get();
        $budgets = Budget::where(['category_id' => $request->child_id])->get();

        if (count($envelopes) > 0) {
            Session::flash('success', 'You can\'t edit category - ' . $childCat->name);
            return redirect()->back();
        }

        if (count($budgets) > 0) {
            Session::flash('success', 'You can\'t edit category - ' . $childCat->name);
            return redirect()->back();
        }
        $childCat->name = $request->edit_child_cat_name;
        $childCat->slug = $request->edit_child_cat_slug;
        if ($request->edit_child_main_cat != 0) {
            $childCat->mainid = $request->edit_child_main_cat;
        }
        $childCat->update();

        Session::flash('success', 'You have successfully updated the category - ' . $childCat->name);
        return redirect()->back();
    }

    public function addCategorySession(Request $request)
    {
        Session::push('category_id_session', $request->id);
    }
    public function removeCategorySession(Request $request)
    {
        $categorySession = Session::get('category_id_session');
        $found = null;
        foreach ($categorySession as $key => $main) {
            if ($main == $request->id) {
                $found = $key;
            }
        }
        Session::pull('category_id_session');
        unset($categorySession[$found]);
        Session::put('category_id_session', $categorySession);
    }
}
