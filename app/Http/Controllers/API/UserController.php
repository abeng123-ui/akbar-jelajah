<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Log;

class UserController extends Controller
{

    public $successStatus = 200;

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('nApp')->accessToken;
            return response()->json(['error' => false, 'message' => 'Success', 'data'=>$success], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function register(Request $request)
    {
        DB::BeginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'role' => 'required|in:admin,user',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }

            $check = User::where('email', $request->email)->first();
            if($check){
                return response()->json(['error'=>'true', 'message' => 'Email already exist'], 401);
            }

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            \Log::Info("Input ".json_encode($input));
            $user = User::create($input);
            \Log::Info("User ".json_encode($user));
            $success['token'] =  $user->createToken('App')->accessToken;
            $success['name'] =  $user->name;

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("{$e->getMessage()}\n{$e->getTraceAsString()}");
            return response()->json(['error' => true, 'message' => "Exception Error"], 401);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("{$e->getMessage()}\n{$e->getTraceAsString()}");
            return response()->json(['error' => true, 'message' => "Throwable Error"], 401);
        }

        return response()->json(['error' => false, 'message' => 'Success', 'data'=>$success], $this->successStatus);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['error' => false, 'message' => 'Success', 'data'=>$user], $this->successStatus);
    }
}
