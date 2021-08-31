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
    @php ($total_entry_amount = 0)
    @php ($total_output_amount = 0)
    @php ($last_balance = 0)
    <table style="font-size:12px;" class="table-info w-100 text-center uppercase my-10">
        <tr class="bg-grey-darker text-white">
            <td style="font-size:80%;">N°</td>
            <td style="font-size:80%;">HORA</td>
            <td style="font-size:80%;">DOC.</td>
            <td style="font-size:80%;">FECHA</td>
            <td style="font-size:80%;">CONCEPTO</td>
            <td style="font-size:80%;">IMPORTE (INGRESO)</td>
            <td style="font-size:80%;">IMPORTE (SALIDA)</td>
            <td style="font-size:80%;">SALDO</td>
        </tr>
        @foreach ( $movement_lists as $movement_list )
        @php ($number += 1)
        <tr>
            <td style="font-size:60%;">{{$number}}</td>
            <td style="font-size:60%;">{{ Carbon::parse($movement_list->created_at)->format('H:i:s') }}</td>
            <td style="font-size:60%;">{{ $movement_list->movement_concept_code}}</td>
            <td style="font-size:60%;">{{ Carbon::parse($movement_list->created_at)->format('d/m/Y') }}</td>
            <td style="font-size:60%;">{{$movement_list->name}}-{{ $movement_list->concept}}</td>
            <td style="font-size:60%;">{{ $movement_list->entry_amount}}</td>
            <td style="font-size:60%;">{{ $movement_list->output_amount}}</td>
            <td style="font-size:60%;">{{ $movement_list->balance}}</td>
        </tr>
        @php ($total_entry_amount += $movement_list->entry_amount)
        @php ($total_output_amount += $movement_list->output_amount)
        @php ($last_balance = $movement_list->balance)
        @endforeach
        <tr class="bg-grey-darker text-s text-white">
                    <td style="font-size:60%;" colspan="5">TOTAL (Bs.)</td>
                    <td style="font-size:60%;" colspan="1" align="right">{{ Util::money_format($total_entry_amount) }}</td>
                    <td style="font-size:60%;" colspan="1" align="right">{{ Util::money_format($total_output_amount) }}</td>
                    <td style="font-size:60%;" colspan="1" align="right">{{ Util::money_format($last_balance) }}</td>
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
                TOTAL INGRESOS BS.
                    <div class="font-semibold leading-tight rounded-full border text-center text-base">{{ Util::money_format($total_entry_amount) }} </div>
                </td>
            </tr>
            <tr>
                <td class="w-25">
                    &nbsp;
                </td>
                <td class="font-semibold w-5">
                TOTAL SALIDAS BS.
                    <div class="font-semibold leading-tight rounded-full border text-center text-base">{{ Util::money_format($total_output_amount) }} </div>
                </td>
            </tr>
            <tr>
                <td class="w-25">
                    &nbsp;
                </td>
                <td class="font-semibold w-5">
                SALDO FINAL BS.
                    <div class="font-semibold leading-tight rounded-full border text-center text-base">{{ Util::money_format($last_balance) }} </div>
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
