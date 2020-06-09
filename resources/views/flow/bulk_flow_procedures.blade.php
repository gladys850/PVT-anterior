<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>PLATAFORMA VIRTUAL ADMINISTRATIVA - MUSERPOL </title>
    <link rel="stylesheet" href="{{ public_path("/css/report-print.min.css") }}" media="all"/>
</head>

<body>
    @php ($plural = count($procedures) > 1)
    @php ($n = 1)
    @php ($hasSender = !is_null($roles['from']))
    @include('partials.header', $header)

    <div class="block">
        <div class="font-semibold leading-tight text-center m-b-10 text-xs">{{ $title }}</div>
    </div>

    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DEL FLUJO</div>
    </div>

    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                @if ($hasSender)
                <td class="w-50">Área Remitente</td>
                @endif
                <td class="{{ $hasSender ? 'w-50' : 'w-100' }}">Área de Destino</td>
            </tr>
            <tr>
                @if ($hasSender)
                <td class="data-row py-5">{{ $roles['from']->display_name }}</td>
                @endif
                <td class="data-row py-5">{{ $roles['to']->display_name }}</td>
            </tr>
        </table>
    </div>

    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DATOS DE{{ $plural ? ' LOS' : 'L' }} TRÁMITE{{ $plural ? 'S' : ''}}</div>
    </div>

    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-20">Código</td>
                <td class="w-25">Solicitante</td>
                <td class="w-10">CI del solicitante</td>
                <td class="w-10">Fecha de solicitud</td>
                <td class="w-10">Monto aprobado</td>
                <td class="{{ $hasSender ? 'w-10' : 'w-5' }}">Plazo</td>
                <td class="{{ $hasSender ? 'w-15' : 'w-10' }}">Tipo de trámite</td>
                @if (!$hasSender)
                <td class="w-10">Remitente</td>
                @endif
            </tr>
            @foreach ($procedures as $loan)
            <tr>
                <td class="data-row py-5">{{ $loan->code }}</td>
                <td class="data-row py-5">{{ $loan->disbursable->title }} {{ $loan->disbursable->full_name }}</td>
                <td class="data-row py-5">{{ $loan->disbursable->identity_card_ext }}</td>
                @php ($created_at = Carbon::parse($loan->created_at))
                <td class="data-row py-5">{{ $created_at->isoFormat('L') }} {{ $created_at->toTimeString() }}</td>
                <td class="data-row py-5">{{ Util::money_format($loan->amount_approved) }}</td>
                <td class="data-row py-5">{{ $loan->loan_term }} Meses</td>
                <td class="data-row py-5">{{ $loan->modality->procedure_type->second_name }}</td>
                @if (!$hasSender)
                <td class="data-row py-5">{{ Role::find($loan->from_role_id)->display_name }}</td>
                @endif
            </tr>
            @endforeach
            <tr>
                <td class="data-row py-5 text-center" colspan="{{ $hasSender ? 7 : 8 }}">
                    <span class="font-semibold uppercase">Total trámites: </span>
                    <span class="font-bold uppercase">{{ count($procedures) }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="text-justify">

    </div>
    <div class="block no-page-break">
        <div class="m-b-10 text-justify">
            Los trámites mencionados en la lista anterior fueron revisados, validados y derivados en sistema. Por tanto, recomiendo dar continuidad al proceso de cada uno de ellos, para lo cual adjunto las carpetas que contienen la información de respaldo.
        </div>

        <div>
            @php($user = Auth::user() ?? (object)['full_name' => 'prueba', 'position' => 'prueba'])
            @include('partials.signature_box', [
                'full_name' => $user->full_name,
                'position' => $user->position,
                'employee' => true
            ])
        </div>
    </div>
</body>
</html>