@extends('mail.layouts.layout')
@section('body')

    <tr>
        <td colspan="2" style="padding: 10px;">
            <b>Estimado/a {{$user->name}},</b>
            <p>¡Estamos encantados de darte la bienvenida a {{env('APP_NAME')}}! 🎊 Gracias por confiar en nosotros,
                estamos
                aquí para asegurarnos de que tengas la mejor experiencia posible.</p>

            <b>A continuación, te compartimos tus datos de acceso:</b>
            <br>
            <b>Usuario:</b> {{$user->email}}
            <br><b>Contraseña:</b> {{$new_pass}}

            <p>Si tienes alguna duda o necesitas ayuda, no dudes en contactarnos al +5354459541 o al email <a
                    href="mailto:cesar.fnts69@gmail.com">cesar.fnts69@gmail.com</a>.
                <br>Nuestro equipo está disponible para
                asistirte en todo lo que necesites.</p>

            <p>Gracias por ser parte de nuestra comunidad. ¡Estamos seguros de que disfrutarás al máximo
                de {{env('APP_NAME')}}!</p>
            <br>
            <br>
            <b>Un cordial saludo,</b>
            <b>El equipo de {{env('APP_NAME')}}</b>

        </td>
    </tr>

@endsection
