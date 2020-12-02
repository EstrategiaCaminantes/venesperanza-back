<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    private $apiToken;
    public function __construct()
    {
    $this->apiToken = uniqid(base64_encode(Str::random(60)));
    }

    public function login(Request $request){

        $user = new User;

        $user->remember_token = $this->apiToken;

        if($user->save()){
            return $user;
        }else{
            return "error";
        };



    }

    public function dashboardLogin(Request $request){

        $user = User::whereEmail($request->email)->first();
        
        if(!is_null($user) && Hash::check($request->password, $user->password)){

            $user['remember_token'] = $this->apiToken;
            $user->save();
            //$token = $user->createToken('Laravel')->accessToken;
            //$token = $this->apiToken;
        
            
            
            return response()->json([
                'res' => $user,
                //'token' => $token,
                'message' => 'Bienvenido al sistema'
            ],200);
        }else{
            return response()->json([
                'res' => false,
                'message' => 'Cuenta o password incorrectos'
            ], 200);
        }


    }


    public function logout($token){
        
        $user = User::where('remember_token',$token)->first();
        
        if($user){
            $user['remember_token'] = null;
            $user->save();
            return response()->json([
                'res' => true,
                'message' => 'success!'
            ], 200);
        }else{
            return response()->json([
                'res' => true,
                'message' => 'success!'
            ], 200);
        }
        
    }

    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
