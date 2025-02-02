@extends('mail.layouts.layout')
@section('body')

    <tr>
        <td colspan="2" style="padding: 10px;">
            <p style="color: #555555;">El usuario <strong>{{$user->name}} se ha autenticado en la app</strong>,</p>

        </td>
    </tr>

@endsection
