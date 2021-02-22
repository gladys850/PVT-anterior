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
                <td class="w-15">Código Tŕamite</td>
                @if ($loan->parent_loan)
                <td class="w-20">Trámite origen</td>
                @endif
                <td class="{{ $loan->parent_loan ? 'w-50' : 'w-75' }}" colspan="{{ $loan->parent_loan ? 1 : 2 }}">Modalidad de trámite</td>
                <td>Tasa Anual</td>
                <td colspan="2">Cuota Fija Mensual</td>
            </tr>
            <tr>
                <td class="data-row py-5 m-b-10 text-xs">{{ $loan->code }}</td>
                @if ($loan->parent_loan)
                <td class="data-row py-5 m-b-10 text-xs">{{ $loan->parent_loan->code }}</td>
                @endif
                <td class="data-row py-5 m-b-10 text-xs" colspan="{{ $loan->parent_loan ? 1 : 2 }}">{{ $loan->modality->name }}</td>
                <td class="m-b-10 text-xs">{{ $loan->interest->annual_interest}}</td>
                <td class="m-b-10 text-xs" colspan="2">{{$loan->estimated_quota}}</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-15">Plazo</td>
                <td class="w-15">Tipo de Desembolso</td>
                <td class="w-15" >Fecha de Desembolso</td>
                <td>Monto Desembolsado</td>  
                <td>Intereses Corrientes Pendientes</td> 
                <td >Intereses Penales Pendientes</td>       
            </tr>
            <tr>          
                <td class="data-row py-5 m-b-10 text-xs">{{ $loan->loan_term }} <span class="capitalize">Meses</span></td>
                <td class="data-row py-5 m-b-10 text-xs">
                    @if($loan->payment_type->name=='Deposito Bancario')
                        <div class="font-bold">Cuenta Banco Union</div>
                        <div>{{ $loan->number_payment_type }}</div>
                    @else
                        {{ $loan->payment_type->name}}
                    @endif
                </td>
                <td class="data-row py-5 m-b-10 text-xs" >{{Carbon::parse($loan->disbursement_date)->format('d/m/y')}}</td>
                <td class="data-row py-5 m-b-10 text-xs">{{ Util::money_format($loan->amount_requested) }} <span class="capitalize">Bs.</span></td>
                <td class="data-row py-5 m-b-10 text-xs">0</td>
                <td class="data-row py-5 m-b-10 text-xs">0</td>
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
                <td class="w-15">Matricula</td>
                <td class="w-15">Sector</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $lender->title }} {{ $lender->full_name }}</td>
                <td class="data-row py-5">{{ $lender->identity_card_ext }}</td>
                <td class="data-row py-5">{{ $lender->registration }}</td>
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
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. KARDEX DE PAGOS (EXPRESADO EN BOLIVIANOS)</div>
    </div>

    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <thead>
                <tr class="bg-grey-darker text-xxs text-white">
                    <th class="w-5">Nº</th>
                    <th class="w-10"><div>Fecha de</div><div>cálculo</div></td>
                    <th class="w-10"><div>Fecha de</div><div>Cobro</div></td>
                    <th class="w-10"><div>Amortización</div><div>capital</div></td>
                    <th class="w-10"><div>Interés</div><div>corriente</div></td>
                    <th class="w-10"><div>Interes</div><div>Penal</div></td>
                    <th class="w-10"><div>Interes Acumulado</div><div>previo</div><div>Nº Dias</div></td> {{-- instres acumulado Nº Dias --}}
                    <th class="w-10"><div>Interes Penal</div><div>previo</div></td>
                    <th class="w-10"><div>Interés</div><div>acumulado</div></td>
                    <th class="w-10"><div>Cuota</div></th>
                    <th class="w-10"><div>Saldo</div><div>Capital</div> </th>
                    <th class="w-10 "><div>Cpte</div> </th>        
                </tr>
            </thead>
            <tbody>
                @php ($sum_accumulated_remaining = 0)
                @php ($sum_accumulated_payment = 0)
                @php ($sum_penal_remaining = 0)
                @php ($sum_penal_payment = 0)
                @php ($sum_interest_payment = 0)
                @php ($sum_capital_payment = 0)
                @php ($sum_estimated_quota = 0)
                @php ($res_saldo_capital = 0)
                @php ($capital = $loan->amount_requested)
                @foreach ($loan->payments->sortBy('quota_number') as $payment)
                @php ($res_saldo_capital = $capital-$payment->capital_payment)
                <tr>
                    <td class="w-5">{{ $payment->quota_number }}</td>
                    <td class="w-10">{{ Carbon::parse($payment->estimated_date)->format('d/m/y') }}</td>
                    <td class="w-10">{{ Carbon::parse($payment->pay_date)->format('d/m/y') }}</td>
                    <td class="w-10 text-right">{{ Util::money_format($payment->capital_payment) }}</td> {{-- capital --}}
                    <td class="w-10 text-right">{{ Util::money_format($payment->interest_payment) }}</td>{{-- instres corriente --}}
                    <td class="w-10 text-right">{{ Util::money_format($payment->penal_payment) }}</td>{{-- instres penal --}}
                    <td class="w-10 text-right">{{ $payment->accumulated_remaining }}</td>{{-- Dias acumulados --}}
                    <td class="w-10 text-right">{{ Util::money_format($payment->penal_remaining) }}</td>{{-- dias verificar --}}
                    <td class="w-10 text-right">{{ Util::money_format($payment->accumulated_payment) }}</td> {{-- interes acumulado--}}
                    <td class="w-10 text-right">{{ Util::money_format($payment->estimated_quota) }}</td>
                    <td class="w-10 text-right">{{ Util::money_format($res_saldo_capital) }}</td>
                    <td class="w-10">{{ $payment->voucher}}</td>
                </tr>
                @php ($capital = $res_saldo_capital)
                @php ($sum_accumulated_remaining += $payment->accumulated_remaining )
                @php ($sum_accumulated_payment += $payment->accumulated_payment)
                @php ($sum_penal_remaining += $payment->penal_remaining)
                @php ($sum_penal_payment += $payment->penal_payment)
                @php ($sum_interest_payment += $payment->interest_payment)
                @php ($sum_capital_payment += $payment->capital_payment)
                @php ($sum_estimated_quota += $payment->estimated_quota)       
                @endforeach
                <tr>
                    <td colspan="3"></td>
                    <td class="w-10 text-right">{{ $sum_capital_payment }}</td>
                    <td class="w-10 text-right">{{ $sum_interest_payment }}</td>
                    <td class="w-10 text-right">{{ $sum_penal_payment }}</td>
                    <td class="w-10 text-right">{{ $sum_accumulated_remaining }}</td>
                    <td class="w-10 text-right">{{ $sum_penal_remaining }}</td>
                    <td class="w-10 text-right">{{ $sum_accumulated_payment }}</td>               
                    <td class="w-10 text-right">{{ $sum_estimated_quota }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>