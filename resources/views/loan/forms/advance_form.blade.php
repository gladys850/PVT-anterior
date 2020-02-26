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
    La Paz, {{ Carbon::now()->isoFormat('LL') }}
    </p>
</div>
<div><br><br>
    <div>Señor</div>
    <div class="font-semibold leading-tight m-b-10 text-xs font-bold uppercase">
        <div>DIRECTOR GENERAL EJECUTIVO</div>
        <div>MUSERPOL</div>
    </div>
    <div>Presente.- </div>
</div><br><br><br>
<div style="block" class="">
    <div class="uppercase font-semibold leading-tight text-right px-100 m-b-10 text-xs">{{ $title }}</div>
</div>
<div class="block text-justify">
    <div>
        <div>
            De mi mayor consideración:
        </div><br>
        <div>
            El objeto de la presente es para solicitar un Préstamo por un monto de Bs. {{ $amount_requested }} (<span class="uppercase">{{ Util::money_format($amount_requested, true) }}</span> Bolivianos) a un plazo de {{$loan_term}} meses,el cual que será aprobado  conforme con los procedimientos del Reglamento de Préstamos vigente en la MUSERPOL.
        </div><br>
        <div>
            El destino del préstamo es para <span class="lowercase font-bold">{{ $destination }}</span>.
        </div><br><br>
        <div>
            Siendo mis datos personales los siguientes:
        </div>
    </div>
    <div>

        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-60">Solicitante</td>
                <td class="w-20">CI</td>
                <td class="w-20">Años de Servicio</td>
            </tr>
            @foreach ($lenders as $lender)
            <tr>
                <td class="data-row py-5">{{ $lender->title }} {{ $lender->full_name }}</td>
                <td class="data-row py-5">{{ ($lender->identity_card_ext) }}</td>
                <td class="data-row py-5">............................</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan = "2" class="w-50">Domilicio actual</td>
                <td class="w-50">Telefonos</td>
            </tr>
            <tr>
                <td colspan = "2" class="data-row py-5">{{ $lender->address? $lender->address->full_address:"" }}</td>
                <td class="data-row py-5">{{ $lender->cell_phone_number}}</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan = "2" class="w-50">Destino Actual</td>
                <td class="w-50">Tipo de Desembolso</td>
            </tr>
            <tr>
                <td colspan = "2" class="data-row py-5">{{ $lender->full_unit }}</td>
                <td colspan="2" class="data-row py-5 normal-case">{{ ($payment_type->name=='Deposito Bancario')? ' Cta. Banco Union: ' .$account_number: $payment_type->name}}</td>
            </tr>
            @endforeach
        </table>
        <table class="table-info w-100 ">
            <tr class="bg-grey-darker text-xxs text-white">
            <td class="data-row py-5"></td>
            </tr>
            <tr>
                <td class="font-semibold leading-tight text-center px-100 m-b-10 text-xs py-10 font-bold">LA PRESENTE SOLICITUD CONSTITUYE DECLARACION JURADA, CONDIGNANDOSE LOS DATOS COMO FIDEDIGNOS POR LOS INTERESADOS.</td>
            </tr>
        
        </table>
    </div><br>
    <div>
        <div>A tal efecto, adjunto los requisito solicitados.</div>
        <div>Sin otro particular, me despido de usted con las consideraciones mas distinguidas.</div>
    </div><br><br><br>
</div><br>
<div class="block">
        <div class='text-center'>
            .................................................<br> 
            FIRMA SOLICITANTE<br>    
        </div><br>
        <div class='text-center'>
        @foreach ($lenders as $lender)
        <tr>
            <td>{{ $lender->title }} {{ $lender->full_name }}</td>
        </tr>
        @endforeach
        </div>   
</div>
</body>
</html>
