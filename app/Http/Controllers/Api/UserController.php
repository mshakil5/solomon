<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AdditionalAddress;
use App\Http\Controllers\Controller;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    
    public function index()
    {
        $userId = Auth::id();
        $userInfo = User::where('id', $userId)->select('id','name','surname','email','email_verified_at','is_type','phone','address_first_line','address_second_line','address_third_line','town','postcode','country','status')
        ->first();

        $defaultShipping = AdditionalAddress::where('user_id', $userId)
        ->where('primary_shipping', 1)
        ->first();

        $defaultBilling = AdditionalAddress::where('user_id', $userId)
        ->where('primary_billing', 1)
        ->first();

        return response()->json([
            'success' => true,
            'response' => [
                'data' => $userInfo,
                'default_shipping' => $defaultShipping,
                'default_billing' => $defaultBilling,
            ]
        ], 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function updateprofileImage(Request $request)
    {
        $userdata = Auth::user();
        $userdata->name = Auth::user()->name;
        $userdata->surname = Auth::user()->surname;
        $userdata->email = Auth::user()->email;
        $userdata->phone = Auth::user()->phone;

        if ($request->image) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $rand = mt_rand(100000, 999999);
            $imageName = time(). $rand .'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $userdata->photo = $imageName;
        }

        if ($userdata->save()) {

            $success['message'] = 'Profile Update Successfully';
            $success['data'] = $userdata;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }
        else{
            
            $success['message'] = 'Server Error!!';
            return response()->json(['success'=>false,'response'=> $success], 204);
        }

    }

    public function userProfileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'phone' => 'required|regex:/^\d{10}$/',
        ], [
            'phone.regex' => 'The phone number must be exactly 10 digits.',
            'email.unique' => 'The email has already been taken.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user()->id);

        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->save();

        return response()->json(['message' => 'Profile updated successfully.', 'user' => $user], 200);
    }

    public function primaryShippingAddressUpdate(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'additional_address_id' => 'required|exists:additional_addresses,id',
      ]);

      if ($validator->fails()) {
          return response()->json([
              'code' => 422,
              'msg' => 'Validation failed',
              'errors' => $validator->errors()
          ], 422);
      }

      $address = AdditionalAddress::where('id', $request->additional_address_id)
      ->where('user_id', Auth::id())
      ->first();

        if (!$address) {
          return response()->json([
            'code' => 404,
            'msg' => 'Address not found or does not belong to the user',
          ], 404);
        }

        AdditionalAddress::where('user_id', Auth::id())->where('primary_shipping', 1)->update(['primary_shipping' => 0]);

        $address->primary_shipping = 1;
        $address->save();

        return response()->json([
            'success' => true,
            'message' => 'Primary address updated successfully',
        ]);

    }

    public function primaryBillingAddressUpdate(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'additional_address_id' => 'required|exists:additional_addresses,id',
      ]);

      if ($validator->fails()) {
          return response()->json([
              'code' => 422,
              'msg' => 'Validation failed',
              'errors' => $validator->errors()
          ], 422);
      }

      $address = AdditionalAddress::where('id', $request->additional_address_id)
      ->where('user_id', Auth::id())
      ->first();

        if (!$address) {
          return response()->json([
            'code' => 404,
            'msg' => 'Address not found or does not belong to the user',
          ], 404);
        }

        AdditionalAddress::where('user_id', Auth::id())->where('primary_billing', 1)->update(['primary_billing' => 0]);

        $address->primary_billing = 1;
        $address->save();

        return response()->json([
            'success' => true,
            'message' => 'Primary billing address updated successfully',
        ]);

    }

    public function address()
    {
        $userId = Auth::id();
    
        $address = AdditionalAddress::where('user_id', $userId)
            ->whereIn('type',[1, 2])
            ->get();
    
        $billing = AdditionalAddress::where('user_id', $userId)
            ->where('type', 2)
            ->get();
    
        return response()->json([
            'addresses' => $address,
        ], 200);
    }    

    public function defaultAddresses()
    {
        $userId = Auth::id();

        $defaultShipping = AdditionalAddress::where('user_id', $userId)
            ->where('primary_shipping', 1)
            ->first();

        $defaultBilling = AdditionalAddress::where('user_id', $userId)
            ->where('primary_billing', 1)
            ->first();

        return response()->json([
            'default_shipping' => $defaultShipping,
            'default_billing' => $defaultBilling,
        ], 200);
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
            return response()->json([
                'code' => 422,
                'msg' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
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
    
        return response()->json([
            'code' => 201,
            'msg' => 'Address created successfully!',
            'address' => $address
        ], 201);
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
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $address = AdditionalAddress::where('id', $id)->first();
        if (!$address) {
            return response()->json(['message' => 'Address not found.'], 404);
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
            'user_id' => Auth::id(),
            'type' => $request->type
        ]);

        return response()->json(['message' => 'Address updated successfully.', 'address' => $address], 200);
    }

    public function destroy($id)
    {
        $address = AdditionalAddress::where('id', $id)->first();
        if (!$address) {
            return response()->json(['message' => 'Address not found.'], 404);
        }
        $address->delete();

        return response()->json(['message' => 'Address deleted successfully.'], 200);
    }

}
