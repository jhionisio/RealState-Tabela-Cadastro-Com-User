<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Api\ApiMessages;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{

    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->user->paginate('10');

        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest   $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();

        if(!$request->has('password') || !$request->get('password')){
            $message = new ApiMessages ('É necessário informar uma senha para o usuário...');
            return response()->json($message, 401);
        }

        try{

            $data['password'] = bcrypt($data['password']);

            $user = $this->user->create($data);

            return response()->json([
                'data' => [
                    'msg' => 'User cadastrado com sucesso'
                ]
            ], 200);
        }catch (Exception $e) {
            $message = new ApiMessages($e->getMessages());
            return response()->json($message->getMessage(), 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $user = $this->user->FindOrFail($id);

            return response()->json([
                'data' => $user

            ], 200);
        } catch (Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $data = $request->all();

        if($request->has('password') && $request->get('password')){
            $data['password'] = bcrypt($data['password']);
        } else{
            unset($data['password']);
        }

        try{

            $user = $this->user->findOrFail($id);
            $user->update($data);

            return response()->json([
                'data' =>[
                    'msg' => 'User atualizado com sucesso'
                ]
                ], 200);

        } catch (Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $realState = $this->user->FindOrFail($id);
            $realState->delete();

            return response()->json([
                'data' => [
                    'msg' => 'User removido com sucesso!'
                ]
            ], 200);
        } catch (Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
