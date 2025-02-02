@php use Illuminate\Support\Carbon; @endphp
@extends('mail.layouts.layout')
@section('body')

    <tr>
        <td colspan="2" style="padding: 10px;">
            <h2>📢 Nueva Queja Recibida</h2>
            <p>Hola Administrador,</p>
            <p>Se ha recibido una nueva queja en el sistema con la siguiente información:</p>

            <strong>Título:</strong> {{ $support->title }}<br>
            <strong>Descripción:</strong>
            <p>{{ $support->text }}</p>

            <p>Por favor, revisa y gestiona esta queja lo antes posible.</p>

            <div class="footer">
                <p>Este es un correo generado automáticamente. Por favor, no responda a este mensaje.</p>
                <p><a href="{{env('APP_URL')}}">Ir al Panel de Administración</a></p>
            </div>
            <p style="color: #333333; font-weight: bold;">El equipo de {{env('APP_NAME')}}</p>
        </td>
    </tr>

@endsection
