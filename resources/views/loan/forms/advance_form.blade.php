<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>PLATAFORMA VIRTUAL ADMINISTRATIVA - MUSERPOL </title>
    <link rel="stylesheet" href="{{ public_path("/css/report-print.min.css") }}" media="all"/>
</head>

<body>
    @include('partials.header', $header)
<div class="text-right">
    <p class="center">
    La Paz, {{ Carbon::parse($request_date)->isoFormat('LL') }}
    </p>
</div>
<div><br><br>
    <div>Señor</div>
    --------------------------------------
    <div style="text-transform: uppercase;" class="font-semibold leading-tight   m-b-10 text-xs">
        <div><b>DIRECTOR GENERAL EJECUTIVO</b></div>
        <div><b>MUSERPOL</b></div>
    </div>
    <div>Presente.- </div>
</div><br><br><br>
<div style = "block" class="">
    <div style="text-transform: uppercase;" class="font-semibold leading-tight text-right px-100 m-b-10 text-xs">{{ $title }}</div>
</div>
<div class="block text-justify">
    <div>
        <div>
            De mi mayor consideración:
        </div><br>
        <div>
            El objeto de la presente es para solicitar un Préstamo por un monto de  {{ $amount_request }} ({{ Util::convertir($amount_request) }} Bolivianos)
        </div>
        <div>
            a un plazo de {{$loan_term}} meses, el cual que será aprobado  conforme con los procedimientos del Reglamento de Préstamos vigente en la MUSERPOL.
        </div>
        <div>
            
        </div><br>
        <div>
            El destino del préstamo es para .......................................
        </div><br><br>
        <div>
            Siendo mis datos personales los siguientes:
        </div>
    </div>
    <div>

        <table class="table-info w-100 text-center uppercase my-20">
        <tr class="bg-grey-darker text-xxs text-white">
            <td class="w-75">Solicitante</td>
            <td class="w-25">CI</td>
        </tr>
        <tr>
            <td class="data-row py-5">X</td>
            <td class="data-row py-5">Y</td>
        </tr>
      
    </table>
    </div>
    <div>
        <div>Nro. Cuenta del Banco Union..........................................</div>
        <div style="text-transform: uppercase;" class="font-semibold leading-tight   m-b-10 text-xs"><b>LA PRESENTE SOLICITUD CONSTITUYE DECLARACION JURADA, CONDIGNANDOSE LOS DATOS COMO FIDEDIGNOS POR LOS INTERESADOS.</b></div>
    </div>
    <div>
        <div>A tal efecto, adjunto los requisito solicitados.</div>
        <div>Sin otro particular, me despido de usted con las consideraciones mas distinguidas.</div>
    </div>
</div><br><br>
<div class="block">
        <div class='text-center'>
            _____________________________<br> 
            FIRMA SOLICITANTE<br>    
        </div>
</div>
</body>
</html>