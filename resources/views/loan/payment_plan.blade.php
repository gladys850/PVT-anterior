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
    </div>

    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DEL TRÁMITE</div>
    </div>

    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-25">Código Tŕamite</td>
                @if ($loan->parent_loan)
                <td class="w-25">Trámite origen</td>
                @endif
                <td class="{{ $loan->parent_loan ? 'w-50' : 'w-75' }}" colspan="{{ $loan->parent_loan ? 1 : 3 }}">Modalidad de trámite</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $loan->code }}</td>
                @if ($loan->parent_loan)
                <td class="data-row py-5">{{ $loan->parent_loan->code }}</td>
                @endif
                <td class="data-row py-5" colspan="{{ $loan->parent_loan ? 1 : 3 }}">{{ $loan->modality->name }}</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td>Monto solicitado</td>
                <td>Plazo</td>
                <td>Tipo de Desembolso</td>
                <td>Fecha de Desembolso</td>

            </tr>
            <tr>
                <td class="data-row py-5">{{ Util::money_format($loan->amount_requested) }} <span class="capitalize">Bs.</span></td>
                <td class="data-row py-5">{{ $loan->loan_term }} <span class="capitalize">Meses</span></td>
                <td class="data-row py-5">
                    @if($loan->payment_type->name=='Deposito Bancario')
                        <div class="font-bold">Cuenta Banco Union</div>
                        <div>{{ $loan->account_number }}</div>
                    @else
                        {{ $loan->payment_type->name}}
                    @endif
                </td>
                <td class="data-row py-5">{{ Carbon::parse($loan->disbursement_date)->format('d/m/y') }}</td>
            </tr>
        </table>
    </div>

    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DE{{ $plural ? ' LOS' : 'L' }} TITULAR{{ $plural ? 'ES' : ''}}</div>
    </div>

    <div class="block">
        @foreach ($lenders as $lender)
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-70">Solicitante</td>
                <td class="w-15">CI</td>
                <td class="w-15">Estado</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $lender->title }} {{ $lender->full_name }}</td>
                <td class="data-row py-5">{{ $lender->identity_card_ext }}</td>
                <td class="data-row py-5">{{ $lender->affiliate_state->affiliate_state_type->name }}</td>
            </tr>
        </table>
        @endforeach
    </div>

    @if ($loan->guarantors()->count())
    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DE{{ $plural ? ' LOS' : 'L' }} GARANTE{{ $plural ? 'S' : ''}}</div>
    </div>

    <div class="block">
        @foreach ($loan->guarantors as $guarantor)
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-70">Garante</td>
                <td class="w-15">CI</td>
                <td class="w-15">Estado</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $guarantor->title }} {{ $guarantor->full_name }}</td>
                <td class="data-row py-5">{{ $guarantor->identity_card_ext }}</td>
                <td class="data-row py-5">{{ $guarantor->affiliate_state->affiliate_state_type->name }}</td>
            </tr>
        </table>
        @endforeach
    </div>
    @endif

    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. PLAN DE PAGOS (EXPRESADO EN BOLIVIANOS)</div>
    </div>
    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <thead>
                <tr class="bg-grey-darker text-xxs text-white">
                    <th class="w-10">Nº</th>
                    <th class="w-15">Fecha</td>
                    <th class="w-15">Cuota</td>
                    <th class="w-15"><div>Días</div><div>interés</div></td>
                    <th class="w-15"><div>Amortización</div><div>capital</div></td>
                    <th class="w-15"><div>Pago</div><div>interés</div></td>
                    <th class="w-15"><div>Saldo</div><div>capital</div></th>
                </tr>
            </thead>
            <tbody>
                @php ($sum_capital = 0)
                @php ($sum_interest = 0)
                @php ($sum_estimated_quota = 0)
                @foreach ($loan->plan as $quota)
                <tr>
                    <td class="data-row py-2">{{ $quota->quota }}</td>
                    <td class="data-row py-2">{{ Carbon::parse($quota->date)->format('d/m/y') }}</td>
                    <td class="data-row py-2">{{ Util::money_format($quota->estimated_quota) }}</td>
                    <td class="data-row py-2">{{ $quota->days }}</td>
                    <td class="data-row py-2">{{ Util::money_format($quota->capital) }}</td>
                    <td class="data-row py-2">{{ Util::money_format($quota->interest) }}</td>
                    <td class="data-row py-2">{{ Util::money_format($quota->next_balance) }}</td>
                </tr>
                @php ($sum_estimated_quota += $quota->estimated_quota)
                @php ($sum_capital += $quota->capital)
                @php ($sum_interest += $quota->interest)
                @endforeach
                <tr>
                    <td colspan="2" class="data-row py-2 font-semibold leading-tight text-xs">TOTALES</td>
                    <td class="data-row py-2">{{ Util::money_format($sum_estimated_quota) }}</td>
                    <td class="data-row py-2"></td>
                    <td class="data-row py-2">{{ Util::money_format($sum_capital) }}</td>
                    <td class="data-row py-2">{{ Util::money_format($sum_interest) }}</td>
                    <td class="data-row py-2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>