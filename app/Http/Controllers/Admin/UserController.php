<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function userList(){
        $userData =User::where('role','user')->get();

        return view('admin.user.userList')->with(['user'=>$userData]);
     }

     public function adminList(){
        $userData =User::where('role','admin')->get();

        return view('admin.user.adminList')->with(['user'=>$userData]);
     }

     public function searchUser(Request $request){

        $response =$this->search('user',$request);

        return view('admin.user.userList')->with(['user'=>$response]);

     }


     public function searchAdmin(Request $request){

        $response =$this->search('admin',$request);
        return view('admin.user.adminList')->with(['user'=>$response]);

    }

    private function search($role,$request){


           $searchData=User::where('role',$role)
                           ->where(function($query) use($request){
                            $query->orwhere('name','like','%'.$request->searchData.'%')
                            ->orwhere('email','like','%'.$request->searchData.'%')
                            ->orwhere('phone','like','%'.$request->searchData.'%')
                            ->orwhere('address','like','%'.$request->searchData.'%');
                           })

                           ->paginate(6);

            $searchData->appends($request->all());
            return $searchData;
    }

     public function adminDelete($id){
        User::where('id',$id)->delete();
        return back()->with(['deleteSuccess'=>'Admin Data deleted!.....']);
     }

     public function userDelete($id){
        User::where('id',$id)->delete();
        return back()->with(['deleteSuccess'=>'User Data deleted!.....']);
     }





}
