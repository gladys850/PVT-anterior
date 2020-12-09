<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>PLATAFORMA VIRTUAL ADMINISTRATIVA - MUSERPOL </title>
    <link rel="stylesheet" href="{{ public_path("/css/report-print.min.css") }}" media="all"/>
</head>
<body>
    @include('partials.header', $header)
<div class="block">
        <div class="font-semibold leading-tight text-center m-b-10 text-xs">FORMULARIO DE CALIFICACIÓN Y APROBACIÓN DE PRÉSTAMO</div>
        @php ($lender = $lenders[0]->disbursable)
</div>
@php ($n = 1)
<div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DEL TRÁMITE</div>
    </div>
<div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-25">Código Tŕamite</td>
                @if ($loan->parent_loan || $loan->parent_reason)
                <td class="w-25">Trámite origen</td>
                @endif
                <td class="{{ $loan->parent_loan ? 'w-50' : 'w-75' }}" colspan="{{ $loan->parent_loan ? 1 : 2 }}">Modalidad de trámite</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $loan->code }}</td>
                @if ($loan->parent_loan)
                <td class="data-row py-5">{{ $loan->parent_loan->code }}</td>
                @endif
                @if ($loan->parent_reason && !$loan->parent_loan_id)
                <td class="data-row py-5">{{ $loan->data_loan->code }}</td>
                @endif
                <td class="data-row py-5" colspan="{{ $loan->parent_loan ? 1 : 2 }}">@if($loan->parent_reason == "REPROGRAMACIÓN") {{$loan->parent_reason}} @endif {{ $loan->modality->name }}</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td>Monto solicitado</td>
                <td>Plazo</td>
                <td>Tipo de Desembolso</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ Util::money_format($loan->amount_requested) }} <span class="capitalize">Bs.</span></td>
                <td class="data-row py-5">{{ $loan->loan_term }} <span class="capitalize">Meses</span></td>
                <td class="data-row py-5">
                    @if($loan->payment_type->name=='Deposito Bancario')
                        <div class="font-bold">Cuenta Banco Union</div>
                        <div>{{ $loan->number_payment_type }}</div>
                    @else
                        {{ $loan->payment_type->name}}
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DE BOLETA</div>
    </div>
<div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="2" >INGRESOS</td>
            </tr>
            <tr >
            <td class="w-50 text-left px-10">LÍQUIDO PAGABLE</td>
            <td class="w-50 text-left">{{ $lender->pivot->payable_liquid_calculated}} </td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="2" class="w-100">EGRESOS</td>
            </tr>
            <tr >
            <td class="w-50 text-left px-10">TOTAL EGRESOS</td>
            <td class="w-50 text-left px-10">{{ $lender->pivot->bonus_calculated}}</td>
            </tr>
            <tr >
            <td class="w-50 text-left px-10">LÍQUIDO PARA CALIFICACIÓN</td>
            <td class="w-50 text-left px-10">{{ $loan->liquid_qualification_calculated }}</td>
            </tr>
        </table>
    </div>
   </div>
   <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. PROPUESTA DE APROBACIÓN</div>
    </div>
    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="2" >PROPUESTA Y APROBACIÓN </td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-left px-10">NOMBRES</td>
            <td class="w-50 text-left px-10">{{ $lender->full_name }}
            </td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-left px-10">INTERÉS CORRIENTE</td>
            <td class="w-50 text-left px-10">{{ $loan->interest->annual_interest }} % Anual</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-left px-10">INTERÉS PENAL</td>
            <td class="w-50 text-left px-10">{{ $loan->interest->penal_interest }} % Anual</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-left px-10">LÍQUIDO PARA CALIFICACIÓN</td>
            <td class="w-50 text-left px-10">{{ $loan->liquid_qualification_calculated }}</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-left px-10">MONTO DEL PRÉSTAMO AUTORIZADO</td>
            <td class="w-50 text-left px-10">{{ $loan->amount_approved }}</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-left px-10">PLAZO EN MESESES</td>
            <td class="w-50 text-left px-10">{{ $loan->loan_term }}</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-left px-10">CUOTA MENSUAL</td>
            <td class="w-50 text-left px-10">{{ $loan->estimated_quota }}</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-left px-10">ÍNDICE DE ENDEUDAMIENTO</td>
            <td class="w-50 text-left px-10">{{ $loan->indebtedness_calculated }}</td>
            </tr>      
        </table>
    </div>
    <table class="text-center">
            <tbody>
                <tr class="align-top">                  
                <td width="35%">                  
                ELABORADO POR
                </td>
                <td width="35%">
                REVISADO POR 
                </td>
                <td width="30%">
                APROBADO POR
                </td>
                </tr>
              
            </tbody>
        </table>

</body>
</html>