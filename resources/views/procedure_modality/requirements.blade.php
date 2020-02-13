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
            <td class="w-75">Solicitante{{ count($lenders) > 0 ? 's' : '' }}</td>
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
            <td colspan="{{ $parent_loan ? 3 : 2 }}">Modalidad de préstamo</td>
        </tr>
        <tr>
            <td colspan="{{ $parent_loan ? 3 : 2 }}" class="data-row py-5">{{ $procedure_modality->name }}</td>
        </tr>
        <tr class="bg-grey-darker text-xxs text-white">
            <td>Monto solicitado</td>
            <td>Plazo</td>
            @if ($parent_loan)
            <td>Trámite origen</td>
            @endif
        </tr>
        <tr>
            <td class="data-row py-5">{{ Util::money_format($amount_request) }} <span class="capitalize">Bs.</span></td>
            <td class="data-row py-5">{{ $loan_term }} <span class="capitalize">Meses</span></td>
            @if ($parent_loan)
            <td class="data-row py-5">{{ $parent_loan->code }}</td>
            @endif
        </tr>
    </table>

    <div class="block">
        <div class="font-semibold leading-tight text-center m-b-10 text-xs">Documentos a presentar</div>
    </div>

    <table class="table-info w-100 text-center uppercase my-20">
        <tr class="bg-grey-darker text-xxs text-white">
            <td colspan="2">Requeridos</td>
        </tr>
        @foreach ($procedure_modality->requirements_list['required'] as $key => $group)
            @foreach ($group as $i => $document)
                <tr>
                    @if ($i == array_key_first($group->all()))
                    <td class="data-row py-5 w-10" rowspan="{{ count($group) }}">{{ $key + 1 }}</td>
                    @endif
                    <td class="data-row py-5 w-90">{{ $document->name }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>

    <table class="table-info w-100 text-center uppercase my-20">
        <tr class="bg-grey-darker text-xxs text-white">
            <td colspan="2">Opcionales</td>
        </tr>
        @foreach ($procedure_modality->requirements_list['optional'] as $key => $document)
            <tr>
                <td class="data-row py-5 w-10">{{ $key + 1 }}</td>
                <td class="data-row py-5 w-90">{{ $document->name }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>