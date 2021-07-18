<?php

namespace App\Http\Controllers\Api;

use App\Events\Chat\SendMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Symfony\Component\httpFoundation\Response; // Chamando a classe de respostas (como as de erros por exemplo)


use App\Models\User; // Importando a model User
use App\Models\Message; // Importando a model Message

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $message = new Message();
        $message->from = Auth::user()->id;
        $message->to = $request->to;
        $message->content = filter_var($request->content, FILTER_SANITIZE_STRIPPED);
        $message->save();

        // Chamando o evento
        Event::dispatch(new SendMessage($message, $request->to)); // Enviando a mensagem (objeto) e quem receberá
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

    public function listMessages(User $user)
    {
        $userFrom = Auth::user()->id; // Id do usuário logado
        $userTo = $user->id;

        /**
         * [from = $userFrom && to = $userTo]
         * OR
         * [from = $userTo && to = $userFrom]
         */

        $messages = Message::where(
            function($query) use ($userFrom, $userTo) { // Esse function é uma clousure
                $query->where([
                    'from' => $userFrom,
                    'to' => $userTo
                ]);
            }
        )->orWhere(
            function($query) use ($userFrom, $userTo) { // Esse function é uma clousure
                $query->where([
                    'from' => $userTo,
                    'to' => $userFrom
                ]);
            }
        )->orderBy('created_at','ASC')->get();

        return response()->json([
            'messages' => $messages
        ],Response::HTTP_OK);
    }
}
