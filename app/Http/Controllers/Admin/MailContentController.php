<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MailContent;
use App\Models\MailContentType;

class MailContentController extends Controller
{
  public function index()
  {

      $data = MailContent::with('type')->latest()->get();
      $mailContentType = MailContentType::where('status', '1')->latest()->get();
      return view('admin.mail_content.index',compact('data', 'mailContentType'));
  }

  public function store(Request $request)
  {
      if(empty($request->mail_content_type_id)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Type \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }

      if(empty($request->subject)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Subject \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }
      
      if(empty($request->content)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }
      
      $data = new MailContent;
      $data->mail_content_type_id = $request->mail_content_type_id;
      $content = html_entity_decode($request->content);
      $data->content = $content;
      $data->subject = $request->subject;
      
      if ($data->save()) {
          $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
          return response()->json(['status'=> 300,'message'=>$message]);
      }else{
          return response()->json(['status'=> 303,'message'=>'Server Error!!']);
      }
  }

  public function edit($id)
  {
      $info = MailContent::find($id);
      return response()->json($info);
  }

  public function update(Request $request)
  {
      if(empty($request->subject)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Subject \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }
      
      if(empty($request->content)){
          $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
          return response()->json(['status'=> 303,'message'=>$message]);
          exit();
      }
      
      $data = MailContent::find($request->codeid);
      $content = html_entity_decode($request->content);
      $data->content = $content;
      $data->subject = $request->subject;
      
      if ($data->save()) {
          $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Update Successfully.</b></div>";
          return response()->json(['status'=> 300,'message'=>$message]);
      }else{
          return response()->json(['status'=> 303,'message'=>'Server Error!!']);
      }
  }
}
