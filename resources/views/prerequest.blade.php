<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ URL::asset('css/materialicons.css') }}" media="all" />
        <link rel="stylesheet" href="{{ URL::asset('css/wkhtml.css') }}" media="all" />
        <title>REQUISITOS PARA PRESTAMOS</title>
    </head>
    <body class="no-border">
        <table class="w-100 ">
            <tr>
                <th class="w-20 text-left no-padding no-margins align-middle">
                    <div class="text-center">
                    <img src="{{URL::asset('img/logo.png')}}" style="width: 200px; height: 100px;">
                    </div>
                </th>
                <th style="font-size: 12pt"class="w-60 align-top">
                    {{ $institution ?? 'MUTUAL DE SERVICIOS AL POLICÍA "MUSERPOL"' }}<br>
                    {{ $direction ?? 'DIRECCIÔN DE ESTRATEGIAS SOCIALES E INVERSIONES' }} <br>
                    {{ $unit ?? 'UNIDAD DE INVERSIÒN EN PRESTAMOS' }}
                </th>
                <td class="w-20 no-padding no-margins align-top">
                    <table class="table-code align-top no-padding no-margins">
                        <tbody>
                            <tr>
                            <div style="font-size: 8pt;text-align:right">
                             <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($hash, 'QRCODE') }}" alt="barcode" width="100" height="100"/>
                              </div>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border-bottom: 2px solid #22292f;"></td> 
            </tr>
            <tr>
       
                <td colspan="3" style="font-size: 12pt"><br>  
                    <b>Nombre:</b> {{$affiliate->first_name}}{{$affiliate->second_name}} {{$affiliate->last_name}} {{$affiliate->mothers_last_name}} <b>CI:</b> {{$affiliate->city_identity_card_id}} {{$affiliate->identity_card}}          
                </td> 
         
            </tr>
            <tr>
                <td colspan="3">
                     <div style="font-size: 12pt;margin-top:0;text-align:center">
                         <p><b>REQUISITOS PARA EL TRAMITE </b><br>{{strtoupper($nommodality)}}</p>
                     </div>
                </td>
            </tr>
        <tr>       
             <table style="font-size: 12pt">
                 <thead>
                    <tr>
                        <th>Nº</th>
                        <th>LISTA DE REQUISITOS</th>
                        <th></th>  
                     </tr>
                </thead>
                 <tbody>
                @foreach ($data as $datos)
                    <tr>
                       <td style="font-size: 12pt">{{$a=$a+1}}</td>
                       <td style="font-size: 12pt">{{$datos}}</td>         
                    </tr>
                @endforeach
                </tbody>    

             </table>
        </tr>
     
        </table> 
    </body>
</html>