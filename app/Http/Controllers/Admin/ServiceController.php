<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Type;

class ServiceController extends Controller
{
  public function index()
  { 
      $types = Type::where('status', 1)->latest()->get();
      $data = Service::orderBy('id', 'DESC')->get();
      return view('admin.service.index', compact('data', 'types'));
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

      if(empty($request->type_id)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Type \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }

      if(empty($request->price)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Price \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }
      
      $data = new Service;
      $data->title_english = $request->title_english;
      $data->title_romanian = $request->title_romanian;
      $data->type_id = $request->type_id;
      $data->des_english = $request->des_english;
      $data->des_romanian = $request->des_romanian;
      $data->information = $request->information;
      $data->price = $request->price;

      if ($request->hasFile('image')) {
          $image = $request->file('image');
          $imageName = rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
          $image->move(public_path('images/service'), $imageName);
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
      $info = Service::where($where)->get()->first();
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
      if(empty($request->type_id)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Type \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }
      if(empty($request->price)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Price \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }
      if(empty($request->codeid)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Code ID \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }


      $data = Service::find($request->codeid);
      $data->title_english = $request->title_english;
      $data->title_romanian = $request->title_romanian;
      $data->type_id = $request->type_id;
      $data->des_english = $request->des_english;
      $data->des_romanian = $request->des_romanian;
      $data->information = $request->information;
      $data->price = $request->price;
      
      if ($request->hasFile('image')) {
          $oldImage = public_path('images/service/' . $data->image);
          if (file_exists($oldImage)) {
              unlink($oldImage);
          }
          $image = $request->file('image');
          $imageName = rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
          $image->move(public_path('images/service'), $imageName);
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
      $data = Service::find($id);
      if($data->image){
        $oldImage = public_path('images/service/' . $data->image);
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
      $data = Service::find($request->id);
      if ($data) {
          $data->status = $request->status;
          $data->save();
  
          return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
      }
      return response()->json(['status' => 404, 'message' => 'Type not found.']);
  }
}
