<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MailContentType;

class MailContentTypeController extends Controller
{
    public function index()
    {
        $data = MailContentType::latest()->get();
        return view('admin.mail_content_type.index',compact('data'));
    }

    public function store(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $check = MailContentType::where('name', $request->name)->first();
        if($check){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This Name Already Exist..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new MailContentType;
        $data->name = $request->name;
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function edit($id)
    {
        $info = MailContentType::find($id);
        return response()->json($info);
    }

    public function update(Request $request)
    {
      if(empty($request->name)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Name \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }

      $check = MailContentType::where('name', $request->name)->where('id', '!=', $request->codeid)->first();
      if($check){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This Name Already Exist..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }

      $data = MailContentType::find($request->codeid);
      $data->name = $request->name;
      if ($data->save()) {
          $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Update Successfully.</b></div>";
          return response()->json(['status'=> 300,'message'=>$message]);
      }else{
          return response()->json(['status'=> 303,'message'=>'Server Error!!']);
      }

    }

    public function delete($id)
    {
        $data = MailContentType::find($id);
        $data->delete();
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Delete Successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_On' => 'required|boolean'
        ]);

        $product = MailContentType::find($request->id);
        $product->status = $request->is_On;
        $product->save();

        return response()->json(['message' => 'Status updated successfully!']);
    }
}   
