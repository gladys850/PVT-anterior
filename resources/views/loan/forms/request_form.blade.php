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
                <td class="{{ $loan->parent_loan ? 'w-50' : 'w-75' }}" colspan="{{ $loan->parent_loan ? 1 : 2 }}">Modalidad de trámite</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $loan->code }}</td>
                @if ($loan->parent_loan)
                <td class="data-row py-5">{{ $loan->parent_loan->code }}</td>
                @endif
                <td class="data-row py-5" colspan="{{ $loan->parent_loan ? 1 : 2 }}">{{ $loan->modality->name }}</td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                <td>Monto solicitado</td>
                <td>Plazo</td>
                <td>Tipo de Desembolso</td>
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
            <tr class="bg-grey-darker text-xxs text-white">
                <td>Domilicio actual</td>
                <td colspan="2">Teléfono(s)</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $lender->address ? $lender->address->full_address : '' }}</td>
                <td class="data-row py-5" colspan="2">
                @if ($lender->phone_number != "" && $lender->phone_number != null)
                    <div>{{ $lender->phone_number }}</div>
                @endif
                @if ($lender->cell_phone_number != "" && $lender->cell_phone_number != null)
                    @foreach(explode(',', $lender->cell_phone_number) as $phone)
                        <div>{{ $phone }}</div>
                    @endforeach
                @endif
                </td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                @php ($inactive = $lender->pension_entity)
                <td colspan="{{$inactive ? 1 : 2}}">Unidad</td>
                <td>Categoría</td>
                @if ($inactive)
                    <td>Tipo de Renta</td>
                @endif
            </tr>
            <tr>
                <td class="data-row py-5" colspan="{{$inactive ? 1 : 2}}">{{ $lender->full_unit}}</td>
                <td class="data-row py-5">{{ $lender->category ? $lender->category->name : '' }}</td>
                @if ($inactive)
                    <td class="data-row py-5">{{ $lender->afp ? $lender->pension_entity ? $lender->pension_entity->name : "APS" : "SENASIR"}}</td>
                @endif
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
            <tr class="bg-grey-darker text-xxs text-white">
                <td>Domilicio actual</td>
                <td colspan="2">Teléfono(s)</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $guarantor->address ? $guarantor->address->full_address : '' }}</td>
                <td class="data-row py-5" colspan="2">
                @if ($guarantor->phone_number != "" && $guarantor->phone_number != null)
                    <div>{{ $guarantor->phone_number }}</div>
                @endif
                @if ($guarantor->cell_phone_number != "" && $guarantor->cell_phone_number != null)
                    @foreach(explode(',', $guarantor->cell_phone_number) as $phone)
                        <div>{{ $phone }}</div>
                    @endforeach
                @endif
                </td>
            </tr>
            <tr class="bg-grey-darker text-xxs text-white">
                @php ($inactive = $guarantor->pension_entity)
                <td colspan="{{$inactive ? 1 : 2}}">Unidad</td>
                <td>Categoría</td>
                @if ($inactive)
                    <td>Tipo de Renta</td>
                @endif
            </tr>
            <tr>
                <td class="data-row py-5" colspan="{{$inactive ? 1 : 2}}">{{ $guarantor->full_unit}}</td>
                <td class="data-row py-5">{{ $guarantor->category ? $guarantor->category->name : '' }}</td>
                @if ($inactive)
                    <td class="data-row py-5">{{ $guarantor->afp ? $guarantor->pension_entity ? $guarantor->pension_entity->name : "APS" : "SENASIR"}}</td>
                @endif
            </tr>
        </table>
        @endforeach
    </div>
    @endif

    @if ($loan->personal_reference)
    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. REFERENCIAS PERSONALES</div>
    </div>

    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td class="w-70">Referencia</td>
                <td class="w-15">CI</td>
                <td class="w-15">Teléfono(s)</td>
            </tr>
            <tr>
                <td class="data-row py-5">{{ $loan->personal_reference->full_name }}</td>
                <td class="data-row py-5">{{ $loan->personal_reference->identity_card_ext }}</td>
                <td class="data-row py-5">
                @if ($loan->personal_reference->phone_number != "" && $loan->personal_reference->phone_number != null)
                    <div>{{ $loan->personal_reference->phone_number }}</div>
                @endif
                @if ($loan->personal_reference->cell_phone_number != "" && $loan->personal_reference->cell_phone_number != null)
                    @foreach(explode(',', $loan->personal_reference->cell_phone_number) as $phone)
                        <div>{{ $phone }}</div>
                    @endforeach
                @endif
                </td>
            </tr>
        </table>
    </div>
    @endif

    <div class="block">
        <div class="font-semibold leading-tight text-left m-b-10 text-xs">{{ $n++ }}. DOCUMENTOS PRESENTADOS</div>
    </div>

    <div class="block">
        <table class="table-info w-100 text-center uppercase my-20">
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="3">Requisitos</td>
            </tr>
            @foreach ($loan->submitted_documents as $key => $document)
                <tr>
                    <td class="data-row py-5 w-10">{{ $key + 1 }}</td>
                    <td class="data-row py-5 w-85">{{ $document->name }}</td>
                    <td class="data-row py-5 w-5">&#10003;</td>
                </tr>
            @endforeach
            @if ($loan->notes()->count())
            <tr class="bg-grey-darker text-xxs text-white">
                <td colspan="3">Otros</td>
            </tr>
            @foreach ($loan->notes as $key => $note)
                <tr>
                    <td class="data-row py-5">{{ $key + 1 }}</td>
                    <td class="data-row py-5" colspan="2">{{ $note->message }}</td>
                </tr>
            @endforeach
            @endif
        </table>
    </div>

    <div class="block text-xs text-justify m-b-10">
        <div class="m-b-10">
            La presente solicitud se constituye en una <span class="font-bold">DECLARACIÓN JURADA</span>, consignandose los datos como fidedignos por los interesados.
        </div>
        <div>
            El suscrito Asistente de Oficina y/o Responsable Regional y/o Atención al Afiliado de la MUSERPOL, CERTIFICA LA AUTENTICIDAD de la documentación presentada y la firma suscrita por {{ $plural ? 'los' : 'el/la' }} Solicitante{{ $plural ? 's' : '' }}, dando FÉ de que la misma fue estampada en mi presencia y en forma voluntaria con puño y letra {{ $plural ? 'de los' : 'del' }} Solicitante{{ $plural ? 's' : '' }}.
        </div>
    </div>

    <hr class="my-20" style="margin-top: 0; padding-top: 0;">

    <div class="block text-xs">
        <div class="m-b-10 text-right">
            La Paz, {{ Carbon::now()->isoFormat('LL') }}
        </div>
        <div>Señor</div>
        <div class="m-b-10 leading-tight font-bold">
            <div>DIRECTOR GENERAL EJECUTIVO</div>
            <div>MUSERPOL</div>
            <div>Presente.- </div>
        </div>
        <div class="m-b-10 text-right leading-tight font-bold">
            REF.: {{ $title }}
        </div>
        <div class="text-justify">
            <div class="m-b-10">
                De mi mayor consideración:
            </div>
            <div class="m-b-10">
                El objeto de la presente es para solicitar un Préstamo por un monto de Bs. {{ $loan->amount_requested }} (<span class="uppercase">{{ Util::money_format($loan->amount_requested, true) }}</span> Bolivianos) a un plazo de {{$loan->loan_term}} meses, el cual que será aprobado conforme con los procedimientos del Reglamento de Préstamos vigente en la MUSERPOL.
            </div>
            <div class="m-b-10">
                El destino del préstamo es <span class="lowercase font-bold">{{ $loan->destiny->name }}</span>. A tal efecto, adjunto los requisitos solicitados y declaro que toda la documentación presentada es veraz y fidedigna; en caso de demostrarse cualquier falsedad, distorsión u omisión en la documentación, reconozco que la Unidad de Inversión en Préstamos procederá a la anulación del trámite y podrá efectuar las acciones correspondientes conforme a los Artículo 17 y 18 de del Capítulo II CONSIDERACIONES DEL PRESTATARIO PARA ACCEDER AL PRÉSTAMO del Reglamento de Préstamos vigente.
            </div>
        </div>
    </div>

    <div class="block no-page-break">
        <div class="m-b-10">
            Sin otro particular, {{ $plural ? 'me despido' : 'nos despedimos'}} de usted con las consideraciones mas distinguidas:
        </div>
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
    </div>
</body>
</html>