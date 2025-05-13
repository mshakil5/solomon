<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdditionalAddress;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        $shipping_addresses = AdditionalAddress::where('user_id', $userId)
            ->where('type', 1)
            ->get();
        
        $billing_addresses = AdditionalAddress::where('user_id', $userId)
            ->where('type', 2)
            ->get();
        
        return view('user.addresses.index', compact('shipping_addresses', 'billing_addresses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'first_line' => 'required|string|max:255',
            'second_line' => 'nullable|string|max:255',
            'third_line' => 'nullable|string|max:255',
            'town' => 'required|string|max:255',
            'post_code' => 'required|string|max:255',
            'floor' => 'nullable|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'type' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $address = new AdditionalAddress([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'phone' => $request->phone,
            'district' => $request->district,
            'first_line' => $request->first_line,
            'second_line' => $request->second_line,
            'third_line' => $request->third_line,
            'town' => $request->town,
            'post_code' => $request->post_code,
            'floor' => $request->floor,
            'apartment' => $request->apartment,
            'user_id' => Auth::id(),
            'type' => $request->type
        ]);

        $address->save();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address created successfully!');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'first_line' => 'required|string|max:255',
            'second_line' => 'nullable|string|max:255',
            'third_line' => 'nullable|string|max:255',
            'town' => 'required|string|max:255',
            'post_code' => 'required|string|max:255',
            'floor' => 'nullable|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'type' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $address = AdditionalAddress::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return redirect()->route('user.addresses.index')
                ->with('error', 'Address not found.');
        }

        $address->update([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'phone' => $request->phone,
            'district' => $request->district,
            'first_line' => $request->first_line,
            'second_line' => $request->second_line,
            'third_line' => $request->third_line,
            'town' => $request->town,
            'post_code' => $request->post_code,
            'floor' => $request->floor,
            'apartment' => $request->apartment,
            'type' => $request->type
        ]);

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address updated successfully.');
    }

    public function destroy($id)
    {
        $address = AdditionalAddress::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return redirect()->route('user.addresses.index')
                ->with('error', 'Address not found.');
        }

        $address->delete();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address deleted successfully.');
    }
}