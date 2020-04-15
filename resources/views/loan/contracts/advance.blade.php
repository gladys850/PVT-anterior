<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>PLATAFORMA VIRTUAL ADMINISTRATIVA - MUSERPOL </title>
    <link rel="stylesheet" href="{{ public_path("/css/report-print.min.css") }}" media="all"/>
</head>
@include('partials.header', $header)
<body>
<div class="block">
    <div class="font-semibold leading-tight text-center m-b-10 text-xs">
        CONTRATO DE PRÉSTAMO <font style="text-transform: uppercase;">{{ $title }}</font>
        <div>Nº {{ $loan->code }}</div>
    </div>
</div>
<div class="block text-justify">
    <div>
        Conste en el presente contrato de préstamo de {{ $title }}, que al solo reconocimiento de firmas y rúbricas ante autoridad competente será elevado a Instrumento Público, por lo que las partes que intervienen lo suscriben al tenor de las siguientes claúsulas y condiciones:
    </div>
    <div>
        <b>PRIMERA.- (DE LAS PARTES):</b> Intervienen en el presente contrato, por una parte como acreedor la Mutual de Servicios al Policía (MUSERPOL), representada legalmente por el {{ $employees[0]['position'] }} Cnl. {{ $employees[0]['name'] }} con C.I. {{ $employees[0]['identity_card'] }} y su {{ $employees[1]['position'] }} Lic. {{ $employees[1]['name'] }} con C.I. {{ $employees[1]['identity_card'] }}, que para fines de este contrato en adelante se denominará MUSERPOL con domiciliio en la Z. Sopocachi, Av. 6 de Agosto Nº 2354 y por otra parte como

        @if (count($lenders) == 1)
        @php ($lender = $lenders[0]->disbursable)
        @php ($male_female = Util::male_female($lender->gender))
        <span>
            DEUDOR{{ $lender->gender == 'M' ? '' : 'A' }} {{ $lender->full_name }}, con C.I. {{ $lender->identity_card_ext }}, {{ $lender->civil_status_gender }}, mayor de edad, hábil por derecho, natural de {{ $lender->city_birth->name }}, vecin{{ $male_female }} de {{ $lender->city_identity_card->name }} y con domicilio especial en {{ $lender->address->full_address }}, en adelante denominad{{ $male_female }} PRESTATARIO.
        </span>
        @endif
    </div>
    <div>
        <b>SEGUNDA.- (DEL OBJETO):</b>  El objeto del presente contrato es el préstamo de dinero que MUSERPOL otorga al PRESTATARIO conforme a niveles de aprobación respectivos, en la suma de Bs. {{ $loan->amount_requested }} (<span class="uppercase">{{ Util::money_format($loan->amount_requested, true) }}</span> Bolivianos).
    </div>
    <div>
        <b>TERCERA.- (DEL INTERÉS):</b> El préstamo objeto del presente contrato, devengará un interés ordinario del {{ $loan->interest->annual_interest }}% anual sobre saldo deudor, el mismo que se recargará con el interés penal en caso de mora de una o más amortizaciones. Esta tasa de interés podrá ser modificada en cualquier momento de acuerdo a las condiciones financieras que adopte MUSERPOL.
    </div>
    <div>
        <b>CUARTA.- (DEL DESEMBOLSO, DEL PLAZO Y LA CUOTA DE AMORTIZACIÓN):</b> El desembolso del préstamo se acredita mediante comprobante escrito en el que conste el abono efectuado a favor del PRESTATARIO, o a través de una cuenta bancaria del Banco Unión a solicitud del PRESTATARIO, reconociendo ambas partes que el amparo de este procedimiento se cumple satisfactoriamente la exigencia contenida en el artículo 1331 del Código de Comercio. El plazo fijo e inprorrogable para el cumplimiento de la obligación contraida por el PRESTATARIO en virtud al préstamo otorgado es de {{ $loan->loan_term }} meses computables a partir de la fecha de desembolso. La cuota de amortización mensual es de Bs. {{ $loan->estimated_quota }} (<span class="uppercase">{{ Util::money_format($loan->estimated_quota, true) }}</span> Bolivianos).
    </div>
    <div>
        Los intereses generados entre la fecha del desembolso del préstamo y la fecha del primer pago serán cobrados con la primera cuota; conforme los establece el reglamento de préstamos.
    </div>
    <div>
        <b>QUINTA.- (DE LA FORMA DE PAGO Y OTRAS CONTINGENGIAS):</b> Para el cumplimiento estricto de la obligación (capital e intereses) el PRESTATARIO, autoriza expresamente a MUSERPOL practicar los descuentos respectivos de los haberes que percibe en forma mensual a través del Comando General de la Policía Boliviana.
    </div>
    <div>
        Si por cualquier motivo la MUSERPOL estuviera imposibilitada de realizar el descuento por el medio señalado, el PRESTATARIO se obliga a cumplir con la cuota de amortización mediante pago directo en la Oficina Central de MUSERPOL de la ciudad de La Paz o efectuar depósito bancario en la cuenta fiscal de la MUSERPOL y enviar la boleta de depósito original a la Oficina Central inmediatamente; sin necesidad de previo aviso; caso contrario el PRESTATARIO se hará pasible al recargo correspondiente a los intereses que se generen al día de pago por la deuda contraida.
    </div>
    <div>
        Asi mismo el prestatario se compromete a hacer conocer oportunamente a MUSERPOL sobre la omisión del descuento mensual que se hubiera dado a efectos de solicitar al Comando General de la Policía Boliviana se regularice este descuento, sin perjuicio que realice el depósito directo del mes omitido, de acuerdo a lo estipulado en el párrafo precedente.
    </div>
    <div>
        <b>SEXTA.- (DERECHOS DEL PRESTATARIO):</b> Conforme al Artículo 29 del Reglamento de Préstamos las partes reconocen expresamente como derechos irrenunciables del PRESTATARIO, lo siguiente:
    </div>
    <div>
        <ol type="a">
            <li>Recibir buena atención, trato equitativo y digno por parte de los funcionarios de la MUSERPOL sin discriminación de ninguna naturaleza y/o por razones de edad, género, raza, religión o identidad cultural, con la debida diligencia según normativa legal vigente y las disposiciones internas de la entidad.</li>
            <li>A recibir los servicios prestados por la MUSERPOL en condiciones de calidez, calidad, oportunidad y disponibilidad, adecuadas a sus intereses económicos.</li>
            <li>A recibir por parte de la MUSERPOL, a través de sus funcionarios, información y orientación precisa en relación a: requisitos, características y condiciones del préstamo; que debe ser fidedigna, amplia, íntegra, clara, comprensible, oportuna y accesible; en caso de duda el afiliado podrá efectuar consultas, peticiones y solicitudes inherentes a los términos y condiciones contractuales del crédito, su situación financiera y legal emergente del mismo.</li>
            <li>A la confidencialidad, en el marco estricto de la normativa legal vigente y reclamar si el servicio recibido no se ajusta a lo previsto en la presente cláusula.</li>
            <li>Otros derechos reconocidos por disposiciones legales y/o reglamentarias, que no sean limitativos ni excluyentes.</li>
        </ol>
    </div>
    <div>
        <b>SÉPTIMA.- (OBLIGACIONES DEL PRESTATARIO):</b> Conforme al Artículo 30 del Reglamento de Préstamos, las partes reconocen expresamente como obligaciones del PRESTATARIO, lo siguiente:
    </div>
    <div>
        <ol type="a">
            <li>Proporcionar a los funcionarios de la MUSERPOL, documentos legítimos, nítidos y legibles para la correcta tramitación de los mismos.</li>
            <li>Presentar documentos idóneos de todos los involucrados en el préstamo solicitado.</li>
            <li>Cumplir con todos y cada uno de los Artículos establecidos en el presente Reglamento de Préstamos.</li>
            <li>Cumplir con los requisitos, condiciones y lineamientos de préstamo.</li>
            <li>Cumplir con el Contrato de Préstamo suscrito entre la MUSERPOL y el PRESTATARIO.</li>
            <li>Amortizar mensualmente la deuda contraida con la MUSERPOL, hasta cubrir el capital adeudado.</li>
            <li>Presentar toda documentación en los plazos previstos por el reglamento.</li>
            <li>Honrar toda deuda pendiente con la MUSERPOL (no estar en mora, al momento de solicitar un nuevo préstamo y/o servicio).</li>
            <li>El deudor debe comunicar a la MUSERPOL, de forma escrita cualquier situación o contingencia. Incluso el cambio de domicilio (ver Art. 15).</li>
        </ol>
    </div>
    <div>
        <b>OCTAVA.- (DE LA GARANTÍA):</b> El PRESTATARIO, garantiza el pago de lo adeudado con todos sus bienes, derechos y acciones habidos y por haber, presentes y futuros conforme determina el Art. 1335 del Código Civil y además con los beneficios del Fondo de Retiro Policial Solidario y Complemento Económico así como establece el Artículo 65 del Reglamento de Préstamos de la MUSERPOL.
    </div>
    <div>
        <b>NOVENA.- (MODIFICACIÓN DE LA SITUACIÓN DEL PRESTATARIO):</b> El PRESTATARIO, en caso de fallecimiento, retiro voluntario o retiro forzoso garantizan con la totalidad de los beneficios de Fondo de Retiro Policial Solidario y Complemento Económico otorgados por la MUSERPOL, el cumplimiento efectivo de la presente obligación; por cuanto la liquidación de dichos beneficios pasarán a cubrir el monto total de la obligación que resulte adecuada, más los intereses devengados en fecha, previas las formalidades de ley.
    </div>
    <div>
        En caso de que se haya modificado la situación del PRESTATARIO del servicio activo al sector pasivo de la Policía Boliviana teniendo un saldo deudor respecto del préstamo obtenido, este acepta amortizar la deuda con su Complemento Económico, en caso de corresponderle debiendo al efecto de solicitar la reprogramación conforme establece el Artículo 90 del Reglamento de Préstamos de la MUSERPOL, por lo que el saldo se sujetará en función al nuevo líquido pagable, estableciendose una correlativa modificación de los plazos y cuota según sea el caso.
    </div>
    <div>
        En caso de incumplimiento de los pagos mensuales estipulados en el presente contrato que generen mora de la obligación, el PRESTATARIO no tendrá dercho a acceder a otro crédito hasta la cancelación total de la deuda.
    </div>
    <div>
        <b>DÉCIMA.- (DE LA CONFORMIDAD Y ACEPTACIÓN):</b> Por una parte en calidad de acreedora la MUSERPOL, representada por su {{ $employees[0]['position'] }} Cnl. {{ $employees[0]['name'] }} y su {{ $employees[1]['position'] }} Lic. {{ $employees[1]['name'] }} y por otra parte en calidad de 
        @if (count($lenders) == 1)
        <span>
            DEUDOR{{ $lender->gender == 'M' ? '' : 'A' }} {{ $lender->full_name }} de generales ya señaladas como PRESTATARIO; damos nuestra plena conformidad con todas y cada una de las cláusulas precedentes, obligándolos a su fiel y estricto cumplimiento. En señal de lo cual suscribimos el presente contrato de préstamo de dinero en manifestación de nuestra libre y espontánea voluntad y sin que medie vicio de consentimiento alguno.
        </span>
        @endif
    </div><br><br>
    <div class="text-center">
        <p class="center">
        La Paz, {{ Carbon::parse($loan->request_date)->isoFormat('LL') }}
        </p>
    </div>
</div>
<div class="block m-t-100">
    <div>
        @include('partials.signature_box', [
            'full_name' => $lender->full_name,
            'identity_card' => $lender->identity_card_ext,
            'position' => 'PRESTATARIO'
        ])
    </div>
    <div class="m-t-75 w-100">
        <table>
            <tr>
                @foreach ($employees as $key => $employee)
                <td>
                    @include('partials.signature_box', [
                        'full_name' => $employee['name'],
                        'identity_card' => $employee['identity_card'],
                        'position' => $employee['position'],
                        'employee' => true
                    ])
                @endforeach
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
