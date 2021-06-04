<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>SOLICITUD DE PRESTAMOS</title>
        <link rel="stylesheet" href="{{ public_path("/css/report-print.min.css") }}" media="all"/>
    </head>
    <body class="no-border">
        <table class="w-100">
        <tbody>
            <tr>    
            <div class="no-padding no-margins text-right">
                <p>{{ date('d') }} de {{ Carbon::now()->formatLocalized('%B') }} de {{ date('Y') }}</p>
                <p><b>FORM:</b>UIP-02</p>
             </div>
             </tr>
            <tr>
            <div class="no-padding no-margins text-left">
                <p><b>Señor:</b></p>
                <p>Cnl. DESP.</p>
                <p><b>DIRECTOR GENERAL EJECUTIVO<br> MUSERPOL</b></p>
                <p>Presente.-</p>
                </div>
            </tr>
            <tr>    
                <div class="no-padding no-margins text-right">
                    <p><b><u>Ref.:SOLICITUD DE PRESTAMO-CORTO PLAZO</u></b></p>
                </div>
            </tr>
            <tr>
            <div class="no-padding no-margins text-justify">
                <p>De mi mayor consideracion:</p>
            </div>
            <div class="no-padding no-margins text-justify">
            <p>El objetivo de la presente es para solicitar un Prestamo con garantia de Personal por un monto de <b>Bs.</b>{{$dat->amount_approved}}(________________________________00/100)Bolivianos a un plazo de____ meses el cual que sera aprobado conforme con los procedimientos del Reglamento de Prestamos vigente en la MUSERPOL.</p>
            <p>El destino del prestamo es para ____________________________________</p>
            <p>Siendo los datos, de mi persona y los garantes los siguientes:</p>
            </div>
            <div style="font-size: 13pt;margin-top: 10px; text-align:justify" >
            <p><b>Solicitante:</b></p>
            </div>
            </tr>
            <tr>
            <div style="font-size: 13pt;margin-top: 10px; text-align:justify">
            <div style="padding:2px;width:200px;float:left;text-align:center">
            <p style="border-bottom-style:dotted;"> </p>
            <p>GRADO</p>
            </div>
            <div style="padding:5px;width:5px;float:left;"></div>
            <div style="padding:2px;width:200px;float:left;text-align:center">
            <p style="border-bottom-style:dotted;">{{$affiliate[0]->last_name}}</p>
            <p>AP.PATERNO</p>
            </div>
            <div style="padding:5px;width:5px;float:left;"></div>
            <div style="padding:2px;width:200px;float:left;text-align:center">
            <p style="border-bottom-style:dotted;">{{$affiliate[0]->mothers_last_name}}</p>
            <p>AP.MATERNO</p>
             </div>
            <div style="padding:5px;width:5px;float:left;"></div>
            <div style="padding:2px;width:250px;float: left;text-align:center">
            <p style="border-bottom-style:dotted;">{{$affiliate[0]->first_name}} {{$affiliate[0]->second_name}}</p>
            <p>NOMBRES</p>
            </div>
            </div>
            </tr>
            <tr>
            <div style="font-size: 13pt;margin-top: 10px;border:1px; text-align:justify;float:center;" >
            <div style="padding:2px;width:30px;float:left;">
            <p>C.I.:</p>
            </div>
            <div style="padding:2px;width:140px;float:left;">
            <br>
            <p style="border-bottom-style:dotted;">{{$affiliate[0]->identity_card}} </p>
            </div>
            <div style="padding:2px;width:30px;float:left;">
            <p>EXT.:</p>
            </div>
            <div style="padding:2px;width:130px;float:left;">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            </div>
            <div style="padding:2px;width:130px;float:left;">
            <p>Destino Actual:</p>
            </div>
            <div style="padding:2px;width:220px;float:left;">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            </div>
            <div style="padding:2px;width:160px;float:left;">
            <p>Años de Servicio:</p>
            </div>
            <div style="padding:2px;width:40px;float:left;">
            <br>
            <p style="border-bottom-style:dotted;">{{$affiliate[0]->service_years}} </p>
            </div>
            </div>
            </tr>
            <tr>
            <div style="font-size: 13pt;margin-top: 10px;border:1px; text-align:justify;float:center" >
            <div style="padding:2px;width:80px;float:left;">
            <p>Domicilio:</p>
            </div>
            <div style="padding:2px;width:170px;float:left;text-align:center">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            <p>CIUDAD</p>
            </div>
            <div style="padding:2px;width:5px;float:left;"></div>
            <div style="padding:2px;width:200px;float:left;text-align:center">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            <p>ZONA</p>
            </div>
            <div style="padding:2px;width:5px;float:left;"></div>
            <div style="padding:2px;width:200px;float:left;text-align:center">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            <p>AV/CALLE</p>
            </div>
            <div style="padding:2px;width:5px;float:left;"></div>
            <div style="padding:2px;width:50px;float: left;text-align:center">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            <p>No.</p>
            </div>
            <div style="padding:2px;width:5px;float:left;"></div>
            <div style="padding:2px;width:150px;float:left;text-align:center">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            <p>CEL./TELEFONO</p>
            </div>
            </div>
            </tr>
            <tr>
            <div style="font-size: 13pt;margin-top: 10px;text-align:justify;float:center" >
            <p><b>Nº Cuenta BANCO UNION (Adj. estracto y o Estado de Cuenta):</b></p>
            </div>
            </tr>
            <tr>
            <div style="font-size: 13pt;margin-top: 10px;text-align:justify;float:center" >
                <p><b>REFERENCIA PERSONAL:</b></p>
            </div>
            </tr>
            <tr>
            <div style="font-size: 13pt;margin-top: 10px;text-align:justify;float:center" >
            <div style="padding:2px;width:80px;float:left;">
            <p>Nombre:</p>
            </div>
            <div style="padding:2px;width:840px;float:left;">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            </div>
            </div>
            </tr>
            <tr>
            <div style="font-size: 13pt;margin-top: 10px;text-align:justify;float:center" >
            <div style="padding:2px;width:80px;float:left;">
            <p>Domicilio:</p>
            </div>
            <div style="padding:2px;width:600px;float:left;">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            </div>
            <div style="padding:2px;width:80px;float:left;">
            <p>Telefono:</p>
            </div>
            <div style="padding:2px;width:150px;float:left;">
            <br>
            <p style="border-bottom-style:dotted;"></p>
            </div>
            </div>
            </tr>
            <div style="font-size: 8pt;margin-top: 10px; text-align:justify;float:center" >
            <p>LA PRESENTE SOLICITUD CONSTITUYE DECLARACION JURADA, CONSIGNADOSE LOS DATOS COMO FIDEDIGNOS POR LOS INTERESADOS.</p>
            </div>        
            <div style="font-size: 12pt;margin-top: 10px; text-align:justify;float:center" >
            <p>A tal efecto, adjunto los requisitos solicitados de mi persona y los garantes.</p>
            </div>
            <div style="font-size: 12pt;margin-top: 10px; text-align:justify;float:center" >
            <p>Sin otro particular, me despido de usted con las consideracion mas distinguidas</p>
            </div>
            <div style="font-size: 12pt;margin-top: 0px; text-align:center" >
            <p>________________________<br>
             FIRMA SOLICITANTE</p>
             <br>
            <p>________________________<br>
              NOMBRES Y APELLIDOS</p>
            </div>
             </tr>
        </table> 
    </body>
</html>