<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;


class CategoryController extends Controller
{
    //direct admin home page

    //direct admin profile

    public function createCategory(Request $request){
       $validator = Validator::make($request->all(),[
        'name'=>'required',
       ]);

       if($validator->fails()){
         return back()
                      ->withErrors($validator)
                      ->withInput();
       }
        $data=['category_name' =>$request->name];
        Category::create($data);
        return redirect()->route('admin#category')
        ->with(['categorySuccess'=>"Category Added...."]);
    }
    public function addCategory(){
        return view('admin.category.addCategory');
    }

    public function deleteCategory($id){
          Category::where('category_id',$id)->delete();
          return back()->with(['deleteSuccess'=>'Category Deleted...']);
    }

    public function editCategory($id){
        $data=Category::where('category_id',$id)->first();
        return view('admin.category.update')->with(['category'=>$data]);
    }

    public function updateCategory(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
           ]);

           if($validator->fails()){
             return back()
                          ->withErrors($validator)
                          ->withInput();
           }
        $updateDate=['category_name' => $request->name];
        Category::where('category_id',$request->id)->update($updateDate);
        return redirect()->route('admin#category')->with(['updateSuccess'=>'Category Updated....']);

    }

    public function searchCategory(Request $request){

             $data=Category::where('category_name','like','%'.$request->searchData.'%')->paginate(7);
             return view('admin.category.list')->with(['category'=>$data]);
    }
    public function category(){

        $data = Category::select('categories.*',DB::raw('COUNT(pizzas.category_id) as count'))
                      ->leftJoin('pizzas','categories.category_id','pizzas.category_id')
                      ->groupBy('categories.category_id')
                      ->paginate(4);

        return view('admin.category.list')->with(['category'=>$data]);
    }

}
