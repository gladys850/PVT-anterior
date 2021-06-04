<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Affiliate;
use App\AffiliateSubmittedDocument;
use App\ScannedDocument;
use App\ProcedureDocument;

/** @group INDEFINIDO (TODO) */
class ScannedDocumentController extends Controller
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

    
    public function create_document($affiliate_id)
    {
        $affiliate =Affiliate::find($affiliate_id);
        $procedure_documents = ProcedureDocument::all();
        $document = ScannedDocument::where('affiliate_id', '=', $affiliate_id)->get();
        $affiliate_submitted_documents = AffiliateSubmittedDocument::where('affiliate_id','=', $affiliate_id)->get();
        return($affiliate_submitted_documents);
        if($document->affiliate_id=$affiliate_id){
            $data = array(
                'affiliate'=>$affiliate,
                'affiliate_submitted_documents'=>$affiliate_submitted_documents,
                'procedure_documents'=>$procedure_documents,
                'document'=>$document
            );
        }
        return view('affiliates.create_scanned_document',$data);
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
        // return $request->all();
        $path = $request->file('archivo')->store('pdfs');
        // return $path;
        $document = new ScannedDocument;
        $document->affiliate_id = $request->affiliate_id;
        $document->url_file = $path;
        $document->procedure_document_id = $request->procedure_document_id;
        $document->comment = $request->comment;
        $document->due_date = $request->due_date;
        $document->save();
        return redirect('affiliate/'.$request->affiliate_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    
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