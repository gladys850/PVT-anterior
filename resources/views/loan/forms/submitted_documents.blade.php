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
        <div class="font-semibold leading-tight text-center m-b-10 text-xs">{{ $title }}</div>
    </div>

    <table class="table-info w-100 text-center uppercase my-20">
        <tr class="bg-grey-darker text-xxs text-white">
            <td class="w-75">Solicitante{{ count($lenders) > 1 ? 's' : '' }}</td>
            <td class="w-25">CI</td>
        </tr>
        @foreach ($lenders as $lender)
        <tr>
            <td class="data-row py-5">{{ $lender->title }} {{ $lender->full_name }}</td>
            <td class="data-row py-5">{{ $lender->identity_card_ext }}</td>
        </tr>
        @endforeach
    </table>

    <table class="table-info w-100 text-center uppercase my-20">
        <tr class="bg-grey-darker text-xxs text-white">
            <td>Código Tŕamite</td>
            <td colspan="{{ $loan->parent_loan ? 2 : 1 }}">Modalidad de trámite</td>
        </tr>
        <tr>
            <td class="data-row py-5">{{ $loan->code }}</td>
            <td colspan="{{ $loan->parent_loan ? 2 : 1 }}" class="data-row py-5">{{ $loan->modality->name }}</td>
        </tr>
        <tr class="bg-grey-darker text-xxs text-white">
            @if ($loan->parent_loan)
            <td>Trámite origen</td>
            @endif
            <td>Monto solicitado</td>
            <td>Plazo</td>
        </tr>
        <tr>
            @if ($loan->parent_loan)
            <td class="data-row py-5">{{ $loan->parent_loan->code }}</td>
            @endif
            <td class="data-row py-5">{{ Util::money_format($loan->amount_requested) }} <span class="capitalize">Bs.</span></td>
            <td class="data-row py-5">{{ $loan->loan_term }} <span class="capitalize">Meses</span></td>
        </tr>
    </table>

    <table class="table-info w-100 text-center uppercase my-20">
        <tr class="bg-grey-darker text-xxs text-white">
            <td colspan="3">Documentos presentados</td>
        </tr>
        @foreach ($loan->submitted_documents as $key => $document)
            <tr>
                <td class="data-row py-5 w-10">{{ $key + 1 }}</td>
                <td class="data-row py-5 w-85">{{ $document->name }}</td>
                <td class="data-row py-5 w-5">&#10003;</td>
            </tr>
        @endforeach
    </table>

    <div class="block text-justify">
        Declaro que toda la documentación presentada es veraz y fidedigna; en caso de demostrarse cualquier falsedad, distorsión u omisión en la documentación, reconozco que la Unidad de Inversión en Préstamos procederá a la anulación del trámite y podrá efectuar las acciones correspondientes conforme a los Artículo 17 y 18 de del Capítulo II CONSIDERACIONES DEL PRESTATARIO PARA ACCEDER AL PRÉSTAMO del Reglamento de Préstamos vigente.
    </div>

    <div class="block m-t-100">
        <table>
            <tbody>
                <tr>
                    <td width="50%">
                        @include('partials.signature_box', [
                            'full_name' => $lender->full_name,
                            'identity_card' => $lender->identity_card_ext,
                            'position' => 'SOLICITANTE'
                        ])
                    </td>
                    <td width="50%">
                        @php($user = Auth::user())>
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