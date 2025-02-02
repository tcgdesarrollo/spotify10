@php use Illuminate\Support\Carbon; @endphp
@extends('mail.layouts.layout')
@section('body')

    <tr>
        <td colspan="2" style="padding: 10px;">
            <div class="header">
                <h2>Pago de Membresía Pendiente</h2>
            </div>
            <div class="content">
                <p>Estimado(a) {{$user->name}},</p>
                @if($days > 0)
                    <p>Hemos detectado que su membresía está pronta a vencer y requiere pago inmediato para evitar la
                        suspensión
                        de su cuenta. Por favor, seleccione la opción de pago que mejor se adapte a su tipo de
                        membresía:</p>
                @else
                    <p>Hemos detectado que su membresía está vencida y requiere pago inmediato para evitar la suspensión
                        de su cuenta. Por favor, seleccione la opción de pago que mejor se adapte a su tipo de
                        membresía:</p>
                @endif
                <div class="membership-links">
                    <ul>
                        @foreach($memberships as $membership)
                            <li>
                                <a href="{{env('APP_URL')}}pago/membresia?user={{$user->token}}&membership={{$membership->id}}">
                                    Pagar Membresía {{$membership->name}} ({{$membership->price}}&euro;)
                                    @if($user->membership_id == $membership->id)
                                        <b>(ACTUAL)</b>
                                    @endif
                                </a></li>
                        @endforeach
                    </ul>
                </div>

                <p>Para cualquier consulta o asistencia, no dude en ponerse en contacto con nuestro equipo de
                    soporte.</p>
                <p>Gracias por su atención.</p>
            </div>
            <p style="color: #333333; font-weight: bold;">El equipo de {{env('APP_NAME')}}</p>
        </td>
    </tr>

@endsection
