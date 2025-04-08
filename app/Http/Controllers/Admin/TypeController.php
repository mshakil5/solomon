<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Type;

class TypeController extends Controller
{
    public function index()
    {
        $data = Type::orderBy('id', 'DESC')->get();
        return view('admin.type.index', compact('data'));
    }

    public function store(Request $request)
    {
        if(empty($request->title_english)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Title English \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->title_romanian)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Title Romanian \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Type;
        $data->title_english = $request->title_english;
        $data->title_romanian = $request->title_romanian;
        $data->des_english = $request->des_english;
        $data->des_romanian = $request->des_romanian;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/type'), $imageName);
            $data->image = $imageName;
        }

        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Created successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Type::where($where)->get()->first();
        return response()->json($info);
    }

    public function update( Request $request)
    {
        if(empty($request->title_english)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Title English \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->title_romanian)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Title Romanian \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->codeid)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Code ID \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $data = Type::find($request->codeid);
        $data->title_english = $request->title_english;
        $data->title_romanian = $request->title_romanian;
        $data->des_english = $request->des_english;
        $data->des_romanian = $request->des_romanian;

        if ($request->hasFile('image')) {
            $oldImage = public_path('images/type/' . $data->image);
            if (file_exists($oldImage)) {
                unlink($oldImage);
            }
            $image = $request->file('image');
            $imageName = rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/type'), $imageName);
            $data->image = $imageName;
        }

        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Updated successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function delete($id)
    {
        $data = Type::find($id);
        if($data->image){
          $oldImage = public_path('images/type/' . $data->image);
          if (file_exists($oldImage)) {
              unlink($oldImage);
          }
        }
        if ($data->delete()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Deleted successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }
    public function toggleStatus(Request $request)
    {
        $data = Type::find($request->id);
        if ($data) {
            $data->status = $request->status;
            $data->save();
    
            return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
        }
        return response()->json(['status' => 404, 'message' => 'Type not found.']);
    }
    
}
