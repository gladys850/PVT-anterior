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

    <div class="block">
        <div class="font-semibold leading-tight text-center m-b-10 text-xs">{{ $title }}</div>
        <div class="font-semibold text-center m-b-3 text-xs">Nro. {{ $loan_payment->code }}</div>
    </div>
    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DEL TRÁMITE</div>
    </div>
    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-white">
                <td class="w-25">Código Tŕamite</td>
                @if ($loan->parent_loan)
                <td class="w-25">Trámite origen</td>
                @endif
                <td class="{{ $loan->parent_loan ? 'w-50' : 'w-75' }}" colspan="{{ $loan->parent_loan ? 1 : 2 }}">Modalidad de trámite</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $loan->code }}</td>
                @if ($loan->parent_loan)
                <td class="data-row py-5">{{ $loan->parent_loan->code }}</td>
                @endif
                <td class="data-row py-5" colspan="{{ $loan->parent_loan ? 1 : 2 }}">{{ $loan->modality->name }}</td>
            </tr>
            @foreach ($lenders as $lender)
            <tr class="bg-grey-darker text-white">
                <td>Fecha de Desembolso</td>
                <td>DATOS DE{{ $plural ? ' LOS' : 'L' }} TITULAR{{ $plural ? 'ES' : ''}}</td>
                <td>CI</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ Carbon::parse($loan->disbursement_date)->format('d/m/y')}}</td>
                <td class="data-row py-5">{{ $lender->title }} {{ $lender->full_name }} ({{ $lender->affiliate_state->affiliate_state_type->name }})</td>
                <td class="data-row py-5">{{ $lender->identity_card_ext }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DEL PAGO</div>
    </div>

    <div class="block">
        <table class="table-info w-100 text-center my-20 uppercase">
            <tr class="bg-grey-darker text-white">
                <td class="w-30">Número de cuota</td>
                <td class="w-35">Saldo Anterior</td>
                <td class="w-35">Fecha de Cálculo</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $loan_payment->quota_number }}</td>
                <td class="data-row py-5">{{ $loan->balance }}</td>
                <td class="data-row py-5">{{ Carbon::parse($loan_payment->estimated_date)->format('d/m/y') }}</td>
            </tr>
            <tr class="">
                <td colspan="2" class="text-left p-10 w-50">Otros Cobros (Interes Acumulado previo, interes penal previo, interes acumulado)</td>
                @php ($total_accumulated = 0)
                @php ($total_accumulated = $loan_payment->accumulated_remaining+$loan_payment->accumulated_payment+$loan_payment->penal_remaining)
                <td class="w-50">{{ Util::money_format($total_accumulated) }}</td>
            </tr>
            <tr class="">
                <td colspan="2" class="text-left p-10 w-50">Interes Penal</td>
                <td class="w-50">{{ Util::money_format($loan_payment->penal_payment) }}</td>

            </tr>
            <tr class="">
                <td colspan="2" class="text-left p-10 w-50">Interes Corriente</td>
                <td class="w-50">{{ Util::money_format($loan_payment->interest_payment) }}</td>

            </tr>
            <tr class="">
                <td colspan="2" class="text-left p-10 w-50">Amortizacion a Capital</td>
                <td class="w-50">{{ Util::money_format($loan_payment->capital_payment) }}</td>

            </tr>
            <tr class="font-semibold leading-tight">
                <td colspan="2" class="text-left p-10 w-50">Total a Pagar (<span class="uppercase font-semibold leading-tight  m-b-10 text-xs">{{ Util::money_format($loan_payment->estimated_quota, true) }} Bolivianos</span> )
                </td>
                <td class="w-50">{{ Util::money_format($loan_payment->estimated_quota) }}</td>
            </tr>
            @if($loan_payment->description)
            <tr>
                <td colspan="3" class="text-left p-10"><span class="font-semibold">Observaciones</span> <br>
                    {{$loan_payment->description}}
                </td>

            </tr>
            @endif

        </table>
    </div>
    <div class="m-t-25 no-page-break">
        <table>
            <tbody>
                @foreach ($signers->chunk(2) as $chunk)
                <tr class="align-top">
                    @foreach ($chunk as $person)
                    <td width="50%">
                        @include('partials.signature_box', $person)
                    </td>
                    @if ($signers->count() % 2 == 1 && $signers->last()['id'] == $person['id'])
                    <td width="50%">
                        @php($user = Auth::user())
                        @include('partials.signature_box', [
                            'full_name' => $user->full_name,
                            'position' => $user->position,
                            'employee' => true
                        ])
                    </td>
                    @endif
                    @endforeach
                </tr>
                @endforeach
                @if ($signers->count() % 2 == 0)
                <tr>
                    <td colspan="2" width="100%">
                        @php($user = Auth::user())
                        @include('partials.signature_box', [
                            'full_name' => $user->full_name,
                            'position' => $user->position,
                            'employee' => true
                        ])
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
        <div class="m-b-10">
            Esta liquidación no es válida sin el Refrendo y Sello correspondiente </div>
    </div>
</body>

</html>