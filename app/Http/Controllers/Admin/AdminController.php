<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function profile(){
        $id=auth()->user()->id;
        $userData=User::where('id',$id)->first();
         return view('admin.profile.index')->with(['user'=>$userData]);
        // return view('admin.profile.index');
     }
     public function update($id,Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'phone'=>'required',
            'address'=>'required',

           ]);

           if($validator->fails()){
            return back()
                         ->withErrors($validator)
                         ->withInput();
          }
          $updateData=$this->requestUserData($request);
          User::where('id',$id)->update($updateData);
          return back()->with(['updateSuccess'=>'User Information Updated']);
 }

     public function changePassword($id,Request $request){
        //$id=auth()->user()->id;
        $validator = Validator::make($request->all(),[
            'oldPassword'=>'required',
            'newPassword'=>'required',
            'confirmPassword'=>'required',
        ]);


        if($validator->fails()){
            return back()
                         ->withErrors($validator)
                         ->withInput();
          }

          $data=User::where('id',$id)->first();

          $oldPassword=$request->oldPassword;
          $newPassword=$request->newPassword;
          $confirmPassword=$request->confirmPassword;

          $hashPassword =$data['password'];

          if(Hash::check($oldPassword,$hashPassword)){//db not same pwd
            if($newPassword != $confirmPassword){
                //new pwd != confirm pwd
                return back()->with(['notSameError'=>'New password must same confirm password!...']);

            }else {
                if(strlen($newPassword) <= 6 || strlen($confirmPassword) <=6){
                    return back()->with(['lengthError'=>'Password must have more than 6 !.....']);
                }else{
                    $hash =Hash::make($newPassword);
                    User::where('id',$id)->update([
                        'password' => $hash
                    ]);
                    return back()->with(['success' =>'Password Changed.....']);
                }
            }

          }else{
            return back()->with(['notMachError'=>'Password do not match! Try Again!....']);
          }
}


 public function changePasswordPage(){

    return view('admin.profile.changePassword');
 }




     private function requestUserData($request){
        return[
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'address'=>$request->address
        ];
     }
}
