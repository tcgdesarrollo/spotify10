@php use Illuminate\Support\Carbon; @endphp
@extends('mail.layouts.layout')
@section('body')

    <tr>
        <td colspan="2" style="padding: 10px;">
            <h2 style="color: #333333; text-align: center;">Aviso de pago completado</h2>
            <p>Hola Administrador,</p>
            <p>Se ha registrado un nuevo pago en el sistema. A continuación, los detalles del pago:</p>
            <div>
                <p><strong>Cliente:</strong> {{$user->fullName}}</p>
                <p><strong>Correo:</strong> {{$user->email}}</p>
                <p><strong>Monto:</strong> €{{round($payment['amount']/100,2)}}</p>
                <p><strong>ID de Orden:</strong> {{$payment['id']}}</p>
                <p><strong>Fecha:</strong> {{Carbon::createFromTimestamp($payment['created'])->format('d/m/Y H:i:s')}}
                </p>
            </div>
            <p>Por favor, revise los detalles y tome las acciones necesarias si es requerido.</p>
            </div>
            <div class="footer">
                <p>Este es un correo generado automáticamente. Por favor, no responda a este mensaje.</p>
                <p><a href="{{env('APP_URL')}}">Ir al Panel de Administración</a></p>
            </div>
            <p style="color: #333333; font-weight: bold;">El equipo de {{env('APP_NAME')}}</p>
        </td>
    </tr>

@endsection
