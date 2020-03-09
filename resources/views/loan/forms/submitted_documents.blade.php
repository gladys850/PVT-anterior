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
            <td colspan="{{ $loan->parent_loan ? 3 : 2 }}">Modalidad de trámite</td>
        </tr>
        <tr>
            <td colspan="{{ $loan->parent_loan ? 3 : 2 }}" class="data-row py-5">{{ $loan->modality->name }}</td>
        </tr>
        <tr class="bg-grey-darker text-xxs text-white">
            <td>Monto solicitado</td>
            <td>Plazo</td>
            @if ($loan->parent_loan)
            <td>Trámite origen</td>
            @endif
        </tr>
        <tr>
            <td class="data-row py-5">{{ Util::money_format($loan->amount_requested) }} <span class="capitalize">Bs.</span></td>
            <td class="data-row py-5">{{ $loan->loan_term }} <span class="capitalize">Meses</span></td>
            @if ($loan->parent_loan)
            <td class="data-row py-5">{{ $loan->parent_loan->code }}</td>
            @endif
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
</body>
</html>