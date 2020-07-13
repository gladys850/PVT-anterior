<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>PLATAFORMA VIRTUAL ADMINISTRATIVA - MUSERPOL </title>
    <link rel="stylesheet" href="{{ public_path("/css/report-print.min.css") }}" media="all"/>
</head>

<body>
    @php ($plural = count($lenders) > 1)
    @php ($n = 1)
    @include('partials.header', $header)
    <div>
        <table>
            <tr>
                <td class="w-25">
                    &nbsp;
                </td>
                <td class="w-50">
                    &nbsp;
                    <div class="font-semibold leading-tight text-center m-b-10 text-3xl">{{ $title }}</div>
                </td>
                <td class="w-25">
                    <div class="font-semibold leading-tight rounded-full border text-center m-b-10 text-2xl">Bs. {{  Util::money_format($loanPayment->voucher->total)}} </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="block">
        <table class="table-info border w-100 my-20 text-2xl">
            <tr>
                <td class="font-semibold w-25">
                    Recibimos de: 
                </td>
                <td class="w-75" colspan="3">
                    {{ $lenders[0]->full_name }}
                </td>
            </tr>
            <tr>
                <td class="font-semibold">
                    La suma de: 
                </td>
                <td class="uppercase" colspan="3">
                {{  Util::money_format($loanPayment->voucher->total, true)}}
                </td>
            </tr>
            <tr>
                <td class="font-semibold">
                    Por Concepto de: 
                </td>
                <td class="" colspan="3">
                    {{ $loanPayment->loan->destiny->name }}
                </td>
            </tr>
            <tr>
                <td class="font-semibold">
                    Forma de Pago: 
                </td>
                <td class="uppercase" colspan="3">
                    {{ $loanPayment->voucher->payment_type->name }}
                </td>
            </tr>
            @if ($loanPayment->voucher->payment_type->id == 2)
                <tr>
                    <td class="font-semibold">Banco: </td>
                    <td class="uppercase">{{ $loanPayment->voucher->bank }}</td>
                    <td class="font-semibold">Número: </td>
                    <td class="">{{ $loanPayment->voucher->bank_pay_number }}</td>
                </tr>
            @endif
        </table>
    </div>
    <br>
    <div>
        <table>
            <tr>
                <td class="w-20">
                    &nbsp;
                </td>
                <td class="w-80">
                    <div class="font-semibold leading-tight text-center m-b-10 text-1xl"><strong>______________________<br>Cobrado por</strong><br>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }} </div>
                </td>
                <td class="w-20">
                    &nbsp;<br><br><br>
                <div class="leading-tight text-center m-b-10 text">{{ Carbon::parse(now())->format('d/m/yy') }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>