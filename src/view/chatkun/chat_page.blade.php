{{--@extends('chatkun.app')--}}
@extends('chatkun.layouts.app')

@section('user_list')
    @foreach($userData as $user)

        <li class="media chat_user" onclick="loadHistoryMessage('{{$user->id}}','{{$user->name}}')"  >
            <a href="#" class="media-link">
                <div class="media-left"><img src="assets/images/placeholder.jpg" class="img-circle img-md" alt=""></div>
                <div class="media-body">
                    <span class="media-heading text-semibold">{{$user->name}}</span>
                    <span class="text-size-small text-muted display-block"></span>
                </div>
                <div class="media-right media-middle">
                    @if($user->online)
                        <span class="status-mark bg-green"></span>
                    @else
                        <span class="status-mark bg-grey "></span>
                    @endif
                </div>
            </a>
        </li>


    @endforeach


@endsection



@section('chat_box')
    @foreach($userData as $user)
        {{--<li class="left clearfix">--}}
        {{--<span class="chat-img1 pull-left">--}}
        {{--<img src="https://lh6.googleusercontent.com/-y-MY2satK-E/AAAAAAAAAAI/AAAAAAAAAJU/ER_hFddBheQ/photo.jpg" alt="User Avatar" class="img-circle">--}}
        {{--</span>--}}
        {{--<div class="chat-body1 clearfix">--}}
        {{--<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia.</p>--}}
        {{--<div class="chat_time pull-right">09:40PM</div>--}}
        {{--</div>--}}
        {{--</li>--}}


    @endforeach


@endsection



@section('script')



@endsection
