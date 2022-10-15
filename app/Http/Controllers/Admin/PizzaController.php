<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Pizza;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PizzaController extends Controller
{
    public function pizza(){
        $data=Pizza::paginate(4);
        if(count($data) == 0){
            $emptyStatus = 0;
        }
        else{
            $emptyStatus = 1;
        }
        return view('admin.pizza.list')->with(['pizza'=>$data,'status' =>$emptyStatus]);
    }

    public function createPizza(){
        $category=Category::get();
        return view ('admin.pizza.create')->with(['category'=>$category]);
    }

    public function insertPizza(Request $request){
       // dd($request->all());


        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'image'=>'required',
            'price'=>'required',
            'publish'=>'required',
            'category'=>'required',
            'discount'=>'required',
            'buyOneGetOne'=>'required',
             'waitingTime'=>'required',
            'description'=>'required',
           ]);

           if($validator->fails()){
             return back()
                          ->withErrors($validator)
                          ->withInput();
           }
           $file=$request->file('image');
           $fileName=uniqid().'_'.$file->getClientOriginalName();
           $file->move(public_path().'/uploads/',$fileName);

           $data=$this->requestPizzaData($request,$fileName);
           Pizza::create($data);
           return redirect()->route('admin#pizza')->with(['createSuccess'=>'Pizza Created!...']);

    }
    private function requestPizzaData($request,$fileName){
        return [
            'pizza_name'=>$request->name,
            'image'=>$fileName,
            'price'=>$request->price,
            'publish_status'=>$request->publish,
            'category_id'=>$request->category,
            'discount_price'=>$request->discount,
            'buy_one_get_one_status'=>$request->buyOneGetOne,
             'waiting_time'=>$request->waitingTime,
            'description'=>$request->description,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ];
    }

    public function infoPizza($id){
        $data=Pizza::where('pizza_id',$id)->first();
        return view('admin.pizza.info')->with(['pizza'=>$data]);
    }

    public function editPizza($id){
        $category=Category::get();
        $data=Pizza::select('pizzas.*','categories.category_id','categories.category_name')
            ->join('categories','pizzas.category_id','=','categories.category_id')
            ->where('pizza_id',$id)
            ->first();

        return view('admin.pizza.edit')->with(['pizza'=>$data,'category'=>$category]);
    }

    public function updatePizza($id,Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'image'=>'required',
            'price'=>'required',
            'publish'=>'required',
            'category'=>'required',
            'discount'=>'required',
            'buyOneGetOne'=>'required',
             'waitingTime'=>'required',
            'description'=>'required',
           ]);

           if($validator->fails()){
             return back()
                          ->withErrors($validator)
                          ->withInput();
           }

        $updateData=$this->requestUpdatePizzaData($request);
        if(isset($updateData['image'])){
            //get old img name
            $data=Pizza::select('image')->where('pizza_id',$id)->first();
            $fileName= $data['image'];

            //delete old image
            if(File::exists(public_path().'/uploads/'.$fileName)){
                File::delete(public_path().'/uploads/'.$fileName);
            }

            //get new image data
            $file= $request->file('image');
            $fileName = uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path().'/uploads/',$fileName);
            $updateData['image']= $fileName;

            //update
            //Pizza::where('pizza_id',$id)->update($updateData);
           // return redirect()->route('admin#pizza')->with(['updateSuccess' => 'Pizza Updated!.....']);
        }

            Pizza::where('pizza_id',$id)->update($updateData);
            return redirect()->route('admin#pizza')->with(['updateSuccess' => 'Pizza Update!......']);

    }

    private function requestUpdatePizzaData($request){
          $arr= [
            'pizza_name'=>$request->name,

            'price'=>$request->price,
            'publish_status'=>$request->publish,
            'category_id'=>$request->category,
            'discount_price'=>$request->discount,
            'buy_one_get_one_status'=>$request->buyOneGetOne,
             'waiting_time'=>$request->waitingTime,
            'description'=>$request->description,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ];
        if(isset($request->image)){
            $arr['image'] =$request->image;
        }
        return $arr;

    }

    public function searchPizza(Request $request){
        $searchKey = $request->table_search;
        $searchData=Pizza::orWhere('pizza_name','like','%'.$searchKey.'%')
                         ->orWhere('price',$searchKey)
                         ->paginate(7);
        if(count($searchData) == 0){
            $emptyStatus = 0;
        }else{
            $emptyStatus =1;
        }
        return view('admin.pizza.list')->with(['pizza'=>$searchData,'status'=>$emptyStatus]);
    }

    public function deletePizza($id){
        $data=Pizza::select('image')->where('pizza_id',$id)->first();
        $fileName=$data['image'];
        Pizza::where('pizza_id',$id)->delete();
        if(File::exists(public_path().'/uploads/'.$fileName)){
            File::delete(public_path().'/uploads/'.$fileName);
        }
        return back()->with(['deleteSuccess'=>'Delete Success!.......']);

    }

    public function categoryItem($id){
        $data=Pizza::select('pizzas.*','categories.category_name as categoryName')
                   ->join('categories','categories.category_id','pizzas.category_id')
                   ->where('pizzas.category_id',$id)
                   ->paginate(5);

        return view('admin.category.item')->with(['pizza'=>$data]);
    }


    }

