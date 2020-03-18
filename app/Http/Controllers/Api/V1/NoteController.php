<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Note;
use Illuminate\Http\Request;
use Carbon;

/** @group Notas aclaratorias
* Datos de las notas aclaratorias de los trámites
*/
class NoteController extends Controller
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
        //
    }

    /**
    * Detalle de nota
    * Devuelve el detalle de una nota aclaratoria
    * @urlParam note_id required ID de nota. Example: 8
    * @authenticated
    * @response
    * {
    *     "id": 8,
    *     "annotable_id": 6,
    *     "annotable_type": "loans",
    *     "message": "BOLETA DE MAYO 2018",
    *     "date": "2018-07-21 11:50:14",
    *     "created_at": "2018-07-21 11:50:14",
    *     "updated_at": "2018-07-21 11:50:14"
    * }
    */
    public function show($id)
    {
        return Note::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function edit(Note $note)
    {
        //
    }

    /**
    * Actualizar nota
    * Actualiza los datos de una nota aclaratoria
    * @urlParam note_id required ID de nota. Example: 8
    * @bodyParam message string required Mensaje de la nota aclaratoria. Example: BOLETA DE MAYO 2018
    * @authenticated
    * @response
    * {
    *     "id": 8,
    *     "annotable_id": 6,
    *     "annotable_type": "loans",
    *     "message": "BOLETA DE MAYO 2018",
    *     "date": "2018-07-21 11:50:14",
    *     "created_at": "2018-07-21 11:50:14",
    *     "updated_at": "2018-07-21 11:50:14"
    * }
    */
    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        $request->validate([
            'message' => 'required|string'
        ]);
        $note->message = $request->message;
        $note->date = Carbon::now();
        $note->save();
        return $note;
    }

    /**
    * Eliminar nota
    * Elimina el registro de una nota aclaratoria
    * @urlParam note_id required ID de nota. Example: 8
    * @authenticated
    * @response
    * {
    *     "id": 8,
    *     "annotable_id": 6,
    *     "annotable_type": "loans",
    *     "message": "BOLETA DE MAYO 2018",
    *     "date": "2018-07-21 11:50:14",
    *     "created_at": "2018-07-21 11:50:14",
    *     "updated_at": "2018-07-21 11:50:14"
    * }
    */
    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();
        return $note;
    }
}
