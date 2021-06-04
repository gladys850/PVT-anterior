<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\NoteForm;
use App\Note;
use Carbon;

/** @group Notas aclaratorias
* Datos de las notas aclaratorias de los trámites
*/
class NoteController extends Controller
{
    /**
    * Detalle de nota
    * Devuelve el detalle de una nota aclaratoria
    * @urlParam note required ID de nota. Example: 8
    * @authenticated
    * @responseFile responses/note/show.200.json
    */
    public function show(Note $note)
    {
        return $note;
    }

    /**
    * Actualizar nota
    * Actualiza los datos de una nota aclaratoria
    * @urlParam note required ID de nota. Example: 8
    * @bodyParam message string required Mensaje de la nota aclaratoria. Example: BOLETA DE MAYO 2018
    * @authenticated
    * @responseFile responses/note/update.200.json
    */
    public function update(NoteForm $request, Note $note)
    {
        $note->message = $request->message;
        $note->date = Carbon::now();
        $note->save();
        return $note;
    }

    /**
    * Eliminar nota
    * Elimina el registro de una nota aclaratoria
    * @urlParam note required ID de nota. Example: 8
    * @authenticated
    * @responseFile responses/note/destroy.200.json
    */
    public function destroy(Note $note)
    {
        $note->delete();
        return $note;
    }
}
