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
        <div class="font-semibold leading-tight text-center m-b-10 uppercase text-xs">{{ $title }}</div>
        @php ($lender = $lenders[0]->disbursable)
</div>
<div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
             <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="2">CALIFICACIÓN DEL TRÁMITE</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="2" >INGRESOS</td>
            </tr>
            <tr >
            <td class="w-50 text-right px-10">LÍQUIDO PAGABLE</td>
            <td class="w-50 text-left">{{ $lender->pivot->payable_liquid_calculated}} </td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="2" class="w-100">EGRESOS</td>
            </tr>
            <tr >
            <td class="w-50 text-right px-10">TOTAL EGRESOS</td>
            <td class="w-50 text-left px-10">{{ $lender->pivot->bonus_calculated}}</td>
            </tr>
            <tr >
            <td class="w-50 text-right px-10">LÍQUIDO PARA CALIFICACIÓN</td>
            <td class="w-50 text-left px-10">{{ $loan->liquid_qualification_calculated }}</td>
            </tr>
        </table>
    </div>
   </div>
    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="2" >PROPUESTA Y APROBACIÓN </td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-right px-10">NOMBRES</td>
            <td class="w-50 text-left px-10">{{ $lender->full_name }}
            </td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-right px-10">INTERÉS CORRIENTE</td>
            <td class="w-50 text-left px-10">{{ $loan->interest->annual_interest }} % Anual</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-right px-10">INTERÉS PENAL</td>
            <td class="w-50 text-left px-10">{{ $loan->interest->penal_interest }} % Anual</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-right px-10">LÍQUIDO PARA CALIFICACIÓN</td>
            <td class="w-50 text-left px-10">{{ $loan->liquid_qualification_calculated }}</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-right px-10">MONTO DEL PRÉSTAMO AUTORIZADO</td>
            <td class="w-50 text-left px-10">{{ $loan->amount_approved }}</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-right px-10">PLAZO EN MESESES</td>
            <td class="w-50 text-left px-10">{{ $loan->loan_term }}</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-right px-10">CUOTA MENSUAL</td>
            <td class="w-50 text-left px-10">{{ $loan->estimated_quota }}</td>
            </tr>
            <tr  class="w-100">
            <td class="w-50 text-right px-10">ÍNDICE DE ENDEUDAMIENTO</td>
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