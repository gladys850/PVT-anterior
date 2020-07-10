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
    <div>
        <table>
            <tr>
                <td class="w-25">
                    &nbsp;
                </td>
                <td class="w-50">
                    &nbsp;
                    <div class="font-semibold leading-tight text-center m-b-10 text-3xl">{{ $title }}</div>
                </td>
                <td class="w-25">
                    <div class="font-semibold leading-tight rounded-full border text-center m-b-10 text-2xl">Bs. 404 </div>
                </td>
            </tr>
        </table>
    </div>

    <div>
        <table>
            <tr>
                <td class="font-semibold">
                    Recibimos de:
                </td>
                <td class="leading-tight">
                    Carlos Ariel Trigo Vargs
                </td>
            </tr>
            <tr>
                <td class="m-b-10">
                    Por Concepto de:
                </td>
                <td class="">
                    Cuatrocientos Cuatro 00/100 Bolivianos
                </td>
            </tr>
            <tr>
                <td class="">
                    Forma de Pago:
                </td>
                <td class="">
                    Efectivo
                </td>
            </tr>
        </table>
    </div>

    <div>
        <table>
            <tr>
                <td class="w-20">
                    &nbsp;
                </td>
                <td class="w-80">
                    <div class="font-semibold leading-tight text-center m-b-10 text-1xl"><strong>______________________<br>Cobrado por</strong><br>Vidal Gonzales </div>
                </td>
                <td class="w-20">
                    &nbsp;
                <div class="leading-tight text-center m-b-10 text">13 de abril 2020</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>