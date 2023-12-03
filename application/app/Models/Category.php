<?php

namespace App\Models;

use App\Models\Envelope;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "categories";
    protected $guarded = [];

    public static function accountingCategories()
    {
         // Get both sub and child categories to one array
         $categories = [];
         $childCategories=[];
         $sub=[];
         $child=[];
         $accountCategory = Category::where('slug', 'accounting')->where('type', 'accounting')->first();
         if ($accountCategory) {
             $categories = \App\Models\Category::where('mainid', $accountCategory->id)->where('role', 'sub')->where('type', 'accounting')->orderBy('name', 'ASC')->get(); 
             foreach ($categories as $key => $value) {
                 $sub[] = explode(",", $value->id);
             }
             $childCategories = \App\Models\Category::whereIn('mainid', $sub)->where('role', 'child')->where('type', 'accounting')->orderBy('name', 'ASC')->get();
             foreach ($childCategories as $key => $value) {
                 $child[] = explode(",", $value->id);
             }
         }
          $categories=array_merge( $sub,$child);
         $catList=$childCategories = \App\Models\Category::whereIn('id',  $categories)->orderBy('name', 'ASC')->get();
         return $catList;
    }

    public function main($id)
    {
        $catName = Category::where('id',$id)->first();
        return $catName->name;
    }

    public function envelope()
    {
        return $this->hasOne(Envelope::class);
    }

    public function budget()
    {
        return $this->hasOne(Budget::class);
    }
}
