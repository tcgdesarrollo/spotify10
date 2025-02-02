@extends('mail.layouts.layout')
@section('body')

    <tr>
        <td colspan="2" style="padding: 10px;">
            Ha solicitado un enlace para restaurar su contraseña.
            <br>Utilice el
            <a style="cursor: pointer; text-decoration: underline"
               href="{{env('APP_URL')}}restaurar-contrasena/{{$user->token}}">siguiente
                enlace</a>
        </td>
    </tr>

@endsection
