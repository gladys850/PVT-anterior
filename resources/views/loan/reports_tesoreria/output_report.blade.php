<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{$file_title}}</title>
    <link rel="stylesheet" href="{{ public_path("/css/report-print.min.css") }}" media="all"/>
</head>
<body>
    @include('partials.header', $header)
<div class="font-hairline leading-tight text-xs text-center">
        <div class="font-semibold leading-tight text-center m-b-10 text-lg">{{ $title }}</div>
        <div>DEL <span class="uppercase">{{ Carbon::parse($initial_date)->isoFormat('LL') }}</span> HASTA EL <span class="uppercase">{{ Carbon::parse($final_date)->isoFormat('LL')}}</span></div>
        <div>(Expresado en Bolivianos)</div>
</div>
<br>
<div class="block">
    @php ($number = 0)
    @php ($total_amount = 0)
    <table style="font-size:12px;" class="table-info w-100 text-center uppercase my-10">
        <tr class="bg-grey-darker text-white">
            <td style="font-size:80%;">N°</td>
            <td style="font-size:80%;">NRO. RECIBO</td>
            <td style="font-size:80%;">FECHA DESEMBOLSO</td>
            <td style="font-size:80%;">COD DE PRÉSTAMO</td>
            <td style="font-size:80%;">NOMBRE(S) Y APELLIDO(S)</td>
            <td style="font-size:80%;">IMPORTE (SALIDA)</td>
        </tr>
        @foreach ( $loans as $loan )
        @php ($number += 1)
        <tr>
            <td style="font-size:60%;">{{$number}}</td>
            <td style="font-size:60%;">{{ $loan['code'] }}</td>
            <td style="font-size:60%;">{{  Carbon::parse($loan['disbursement_date_loan'])->format('d/m/Y H:i:s') }}</td>
            <td style="font-size:60%;">{{ $loan['code_loan'] }}</td>
            <td style="font-size:60%;">{{ $loan['full_name_borrower'] }}</td>
            <td style="font-size:60%;">{{ Util::money_format($loan['amount_approved_loan']) }}</td>

        </tr>
        @php ($total_amount += $loan['amount_approved_loan'])
        @endforeach
        <tr class="bg-grey-darker text-s text-white">
                    <td style="font-size:60%;" colspan="5">TOTAL (Bs.)</td>
                    <td style="font-size:60%;" colspan="1" align="right">{{ Util::money_format($total_amount) }}</td>
        </tr>
    </table>
</div>
<br>
<div class="block">
        <table style="font-size:10px;" class="my-5">
            <tr>
                <td class="w-25">
                    &nbsp;
                </td>
                <td class="font-semibold w-5">
                TOTAL SALIDAS
                    <div class="font-semibold leading-tight rounded-full border text-center text-base">{{ Util::money_format($total_amount) }} </div>
                </td>
            </tr>
        </table>
    </div>

<div class="no-page-break">
        <table style="font-size:10px;">
            <tbody>
            <tr height="25px" class="">
               <td>
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
</body>
</html>
