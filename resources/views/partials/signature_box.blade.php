<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>

<body class="w-100">
    <div class='text-center'>
        @for ($i=0; $i < strlen($full_name) - 8; $i++)
        _
        @endfor
        <br>
        {{ $full_name }}<br>
        @if(isset($identity_card))
            C.I. {{ $identity_card }}<br>
        @endif
        <b>{{ $position }}</b>
        @if(isset($employee))
        @if($employee)
            <br>MUSERPOL
            @endif
        @endif
    </div>
</body>
</html>