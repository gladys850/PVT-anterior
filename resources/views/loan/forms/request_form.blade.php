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
    <div class="uppercase font-semibold leading-tight text-right px-4 text-xs">{{ $title }}</div>
</div>
<div class="block text-justify">
    <div>
        <div>
            De mi mayor consideración:
        </div><br>
        <div>
            El objeto de la presente es para solicitar un Préstamo por un monto de Bs. {{ $loan->amount_requested }} (<span class="uppercase">{{ Util::money_format($loan->amount_requested, true) }}</span> Bolivianos) a un plazo de {{$loan->loan_term}} meses,el cual que será aprobado  conforme con los procedimientos del Reglamento de Préstamos vigente en la MUSERPOL.
        </div><br>
        <div>
            El destino del préstamo es para <span class="lowercase font-bold">{{ $loan->destination->name }}</span>.
        </div><br><br>
        <div>
            Siendo mis datos personales los siguientes:
        </div>
    </div>
    <div>
        @foreach ($lenders as $lender)
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-50">Solicitante</td>
                <td class="w-20">CI</td>
                <td class="w-30">Tipo de Renta</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $lender->title }} {{ $lender->full_name }}</td>
                <td class="data-row py-5">{{ ($lender->identity_card_ext) }}</td>
                <td class="data-row py-5">{{ $lender->afp? $lender->pension_entity? $lender->pension_entity->name:"APS":"SENASIR"}}</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan = "2" class="w-50">Años de Servicio</td>
                <td class="w-50">Categoría</td>
            </tr>
            <tr>
                <td colspan = "2" class="data-row py-5"></td>
                <td class="data-row py-5">{{ $lender->category? $lender->category->name:'' }}</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan = "2" class="w-50">Domilicio actual</td>
                <td class="w-50">Teléfonos</td>
            </tr>
            <tr>
                <td colspan = "2" class="data-row py-5">{{ $lender->address? $lender->address->full_address:'' }}</td>
                <td class="data-row py-5">
                @if ($lender->cell_phone_number != "")
                @foreach(explode(',', $lender->cell_phone_number) as $phone)
                    {{ $phone }}<br>
                @endforeach
                @endif
                </td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan = "2" class="w-50">Unidad</td>
                <td class="w-50">Tipo de Desembolso</td>
            </tr>
            <tr>
                <td colspan = "2" class="data-row py-5">{{ $lender->full_unit}}</td>
                <td colspan="2" class="data-row py-5 normal-case">
                @if($loan->payment_type->name=='Deposito Bancario')
                    <b>Cuenta Banco Union</b><br>
                    {{ $loan->account_number }}
                @else
                    {{ $loan->payment_type->name}}
                @endif
                </td>
            </tr>
        </table>
        @endforeach
        <table class="table-info w-100 text-center uppercase my-20 ">
            <tr class ="bg-grey-lightest text-black">
                <td class="font-semibold text-center  text-xs py-10">LA PRESENTE SOLICITUD CONSTITUYE DECLARACION JURADA, CONDIGNANDOSE LOS DATOS COMO FIDEDIGNOS POR LOS INTERESADOS.</td>
            </tr>
        </table>
    </div>
    <div>
        <div>A tal efecto, adjunto los requisitos solicitados.</div>
        <div>Sin otro particular, me despido de usted con las consideraciones mas distinguidas.</div>
    </div>
</div>
<div class="block m-t-100">
        <table>
            <tbody>
                <tr>
                    <td width="50%">
                    @foreach ($lenders as $lender)
                        @include('partials.signature_box', [
                            'full_name' => $lender->full_name,
                            'identity_card' => $lender->identity_card_ext,
                            'position' => 'SOLICITANTE'
                        ])
                    @endforeach
                    </td>
                    <td width="50%">
                        @php($user = Auth::user())
                        @include('partials.signature_box', [
                            'full_name' => $user->full_name,
                            'position' => $user->position,
                            'employee' => true
                        ])
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<div class="block text-justify font-bold">
    @foreach ($lenders as $lender)
    @php ($male_female = Util::male_female($lender->gender))
    <div><h5>El suscrito: Asistente de Oficina y/o Responsable Regional y/o Atención al Afiliad{{ $male_female}} de la MUSERPOL, CERTIFICA la AUTENTICIDAD de la firma y documentación presentada por el Afiliad{{ $male_female}}, dando FÉ que la misma fue estampada en mi presencia y en forma voluntaria con puño y letra del Afiliad{{ $male_female}}
        {{ $lender->title }} {{ $lender->full_name }} C.I. N° {{ $lender->identity_card_ext }}.<br>
    @endforeach
    Asimismo, el solicitante del Préstamo da FÉ de que la documentación y firmas son auténticas y obtenidas lícitamente.</h5></div>
</div>
</body>
</html>
