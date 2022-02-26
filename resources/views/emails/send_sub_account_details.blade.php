@extends('master_email')
@section('content')
    <tr>
        <td style="background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
            <div style="display: block; margin: 0px; padding:40px; padding-top: 20px; background-color:#fff; font-family: 'Avenir LT Std', sans-serif; ">
                <div style="text-align: center;">
                    <h2 style="color: #121212; font-weight: 600; font-size: 1.5rem;">Hi {{ $name }}</h2>
                    <br>
                    <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Your account details are as follows:</p>
                    <br>
                    <div style="max-width: 65%; margin:0 auto;">
                        <p style="max-width: 75%; line-height: 1.5; font-weight: 300; margin: 0 auto; color: #121212;">Account No: {{$account}}.</p>
                    </div>
                    <br>
                    {{--                    <div>--}}
                    {{--                        <span style="color: #707070; padding-top:50px; padding-bottom: 5px; display: block;">Or use this link to login</span>--}}
                    {{--                        <a style="color: #00ACCB; font-size: 14px ;max-width: 75%; margin: 0 auto; line-height: 1.5; display: block; " href="{{ url('login') }}">https://santa.crowdyvest.com/login</a>--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </td>
    </tr>
@endsection
