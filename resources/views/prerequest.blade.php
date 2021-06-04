<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>SOLICITUD DE PRESTAMOS</title>
        <link rel="stylesheet" href="{{ public_path("/css/report-print.min.css") }}" media="all"/>
    </head>
    <body class="no-border">
        <table class="w-100">
            <tr>
              <th class="w-20 text-left no-padding no-margins align-middle">
                    <div class="text-left">
                    <img src="{{URL::asset('img/logo.png')}}" class="w-100">
                    </div>
              </th>
            <th class="w-50 align-top">
            <div class="font-semibold uppercase leading-tight text-md" >
            <div>{{ $institution ?? 'MUTUAL DE SERVICIOS AL POLICÍA "MUSERPOL"' }}</div>
            <div>{{ $direction ?? 'DIRECCIÔN DE ESTRATEGIAS SOCIALES E INVERSIONES' }} </div>
            <div>{{ $unit ?? 'UNIDAD DE INVERSIÒN EN PRESTAMOS' }}</div>
            </div>
            </th>
            <td class="w-20 no-padding no-margins align-top">
            <table class="table-code no-padding no-margins">
            <tbody>
              <tr>
                  <td class="text-center bg-grey-darker text-xxs text-white">Àrea </td>
                  <td class="text-xs">Plataforma</td>
              </tr>
              <tr>
                <td class="text-center bg-grey-darker text-xxs text-white">Fecha</td>
                <td class="text-xs">{{ Carbon::now()}}</td>
              </tr>
            </tbody>
             </table>
            </td>         
            </tr>
            <tr>
                <td colspan="3" style="border-bottom: 2px solid #22292f;"></td> 
            </tr>
            <tr>
                <td colspan="3">
                <div class="font-semibold leading-tight text-sm text-center m-b-10">REQUISITOS PARA EL TRAMITE</div>
                <div class="leading-tight text-sm text-center m-b-10">{{strtoupper($nommodality)}}</div>
                </td>
            </tr>
        <tr>  
        <div class="block">
        <table class="table-code w-100 m-b-10 uppercase text-xs">
        <tbody>
          <tr>
            <td class="w-10 text-center bg-grey-darker text-white font-light">Nombre</td>
            <td class="w-90 font-thin p-l-5">{{$affiliate->first_name}}{{$affiliate->second_name}} {{$affiliate->last_name}} {{$affiliate->mothers_last_name}} </td>
          </tr>
          <tr>
            <td class="w-10 text-center bg-grey-darker text-white font-light">CI</td>
            <td class="w-90 font-thin p-l-5">{{$affiliate->city_identity_card_id}} {{$affiliate->identity_card}} </td>
          </tr>
        </tbody>
      </table>  
      <table class="table-info w-100 m-b-10 uppercase text-xs">
        <thead>
          <tr>
            <th class="text-center bg-grey-darker text-white">Nº</th>
            <th class="text-center bg-grey-darker text-white border-left-white">LISTA DE REQUISITOS</th>
          </tr>
        </thead>
        <tbody class="table-striped">
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
<table>
      <tr>
        <td class="text-xxxs" align="left">
          @if (env("APP_ENV") == "production")
            Documentos para la Solicitud de Prestamo.
          @else
            VERSIÓN DE PRUEBAS
          @endif
        </td>
        <td class="child" align="right">
        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($hash, 'QRCODE') }}" alt="barcode" width="100" height="100"/>
        </td>
      </tr>
    </table>