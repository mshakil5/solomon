<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index($id)
    {
        $work = Work::findOrFail($id);
        $data = $work->transactions;
        return view('admin.work.transactions.index', compact('data', 'work'));
    }

    public function store(Request $request)
    {
        if(empty($request->date)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->amount)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Amount \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $work = Work::findOrFail($request->work_id);

        $transaction = new Transaction();
        $transaction->date = $request->date;
        $transaction->amount = $request->amount;
        $transaction->work_id = $work->id;
        $transaction->user_id = $work->user_id; 

        $timestamp = now()->format('YmdHis');
        $randomDigits = rand(1000, 9999);
        $transaction->tranid = $timestamp . $randomDigits;

        if ($transaction->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Transaction Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        };
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Transaction::where($where)->get()->first();
        return response()->json($info);
    }
    
    public function update(Request $request)
    {
        if(empty($request->date)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->amount)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Amount \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $transaction = Transaction::find($request->codeid);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $transaction->date = $request->date;
        $transaction->amount = $request->amount;
        $transaction->save();

        if ($transaction->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);$message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        } 
        else {
           return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function destroy($id)
    {
        if(Transaction::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Data has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }

    public function showTransactions($id)
    {
        $work = Work::findOrFail($id);
        $data = $work->transactions;
        return view('admin.work.transactions.index', compact('data','work'));
    }
    
    public function addTransaction($work_id)
    {
        return view('admin.work.transaction.create', compact('work_id'));
    }

    public function allTransactions()
    {
        $data = Transaction::with(['invoice.serviceBooking', 'booking'])->orderBy('id', 'desc')->get();
        return view('admin.work.transaction.list', compact('data'));
    }

}
