

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Chat online</title>


    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/icons/icomoon/styles.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/bootstrap.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/core.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/components.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/colors.css')}}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/loaders/pace.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/loaders/blockui.min.js')}}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script type="text/javascript" src="{{asset('assets/js/core/app.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/chat_layouts.js')}}"></script>

    <script type="text/javascript" src="{{asset('assets/js/plugins/ui/ripple.min.js')}}"></script>
    <!-- /theme JS files -->
    <script src="https://js.pusher.com/4.0/pusher.min.js"></script>
    {{--<script src="{{asset('lib/enyo/dropzone/master/dist/dropzone.js')}}"></script>--}}
    {{--<link rel="stylesheet" href="{{asset('lib/enyo/dropzone/master/dist/dropzone.css')}}">--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.js"></script>
    <script>






        // Enable pusher logging - don't include this in production
        var StartUpUser='{{$user_id}}';
        var StartUpUserName='{{$name}}';
        StatusNotify=true;
        Pusher.logToConsole = true;

        var to_user_id;
        var pusher = new Pusher('71baa59665e9ba7ac6e9', {
            cluster: 'ap1',
            encrypted: true
        });

        var channel = pusher.subscribe('{{Auth::user()->id}}');
        channel.bind('my-event', function(data) {
            if(to_user_id==data.from){
                create_message_timeline(data.message,'right',data.type,data.link);
                $("#chat_area").animate({ scrollTop: 10000 }, "fast");

            }
            console.log("ssss");
            if(data.type=="file"){
                notifyMe(data.name+" ได้ส่งข้อไฟล์ให้คุณ",'{{url('chatkun')."?user_id="}}'+data.from+"&name="+data.name);

            }else{
                notifyMe(data.message,'{{url('chatkun')."?user_id="}}'+data.from+"&name="+data.name);

            }
        });
        function loadHistoryMessage(user_id,user_name){
            loadUserMessage(user_name,user_id);
        }

       function sendMessage(){
            create_message_timeline($(".message_input").val(),'left','message','');
            $("#chat_area").animate({ scrollTop: 10000 }, "fast");
            message_input =$(".message_input").val();
            $(".message_input").val('');
            $.ajax({
                method: "GET",
                url: "{{url('chatkun/send')}}",
                data:{
                    _token: "{{ csrf_token() }}",
                    to_user_id:to_user_id,
                    message:message_input

                }
            }).done(function( data ) {


            });
        }
        function loadUserMessage(user_name,user_id){
            $("#chat_area").html('');
            $(".message_input").prop("disabled",false);
            $("#fileinput-button").prop("disabled",false);
            $("#btnSend").prop("disabled",false);
            $("#user_name").text(user_name);
            $('#to_user_id').val(user_id);
            to_user_id=user_id;
            $.ajax({
                method: "GET",
                url: "{{url('chatkun/history')}}/"+user_id
            })
                    .done(function( data ) {

                        console.log(data);

                        for(var index=0;index<data.length;index++){
                            if(data[index].user_id==$("#user_id").val()){
                                create_message_timeline(data[index].message,'left',data[index].type,data[index].option);

                            }else{
                                create_message_timeline(data[index].message,'right',data[index].type,data[index].option);

                            }

                        }
                        $("#chat_area").fadeIn(500);
                    });

            $("#chat_area").animate({ scrollTop: 10000 }, "fast");
        }
        function create_message_timeline(message, side,type,linkdownload) {
           if(type=='file'){
               message='<a   href="'+linkdownload+'"><i class="icon-file-download"></i> Download file</a>';
           }
            if (side == "right") {

                $("#chat_area").append('<li class="media reversed">' +
                        '<div class="media-body">' +
                        '<div class="media-content">' + message + '</div>' +
//                                    '<span class="media-annotation display-block mt-10">'+time+' <a href="#"><i class="icon-pin-alt position-right text-muted"></i></a></span>' +
                        '</div>' +

                        '<div class="media-right">' +
                        '<a href="assets/images/placeholder.jpg">' +
                        '<img src="assets/images/placeholder.jpg" class="img-circle img-md" alt="">' +
                        '</a>' +
                        '</div>' +
                        '</li>');
            } else if(side=="left") {
                $("#chat_area").append('<li class="media">' +
                        '<div class="media-left">' +
                        '<a href="assets/images/placeholder.jpg">' +
                        '<img src="assets/images/placeholder.jpg" class="img-circle img-md" alt="">' +
                        '</a>' +
                        '</div>' +

                        '<div class="media-body">' +
                        '<div class="media-content">' + message + '</div>' +
//                                    '<span class="media-annotation display-block mt-10">'+time+'<a href="#"><i class="icon-pin-alt position-right text-muted"></i></a></span>' +
                        '</div>' +
                        '</li>');


            }

        }
        function notifyMe(message,link) {
            if(!StatusNotify){

                if (!Notification ) {
                    alert('Desktop notifications not available in your browser. Try Chromium.');
                    return;
                }

                if (Notification.permission !== "granted")
                    Notification.requestPermission();
                else {
                    var notification = new Notification("คุณมีข้อความใหม่", {
                        icon: '{{url('/')}}/assets/images/placeholder.jpg',
                        body: message
                    });

                    notification.onclick = function () {
                        window.open(link);
                    };

                }

            }


        }
        function checkTab()
        {
            console.log(document.hasFocus());
            if(document.hasFocus()){
                StatusNotify=true;
            }else{
                StatusNotify=false;
            }
        }




        setInterval(checkTab, 200);
        $(document).ready(function() {
            $('.message_input').keypress(function (event) {
                if (event.keyCode == '13') {
                    sendMessage();
                }
            });


            if(StartUpUser!=""){
                loadUserMessage(StartUpUserName,StartUpUser);
            }else{
                $("#user_name").text("กรุณาเลือกคู่เเชท");
                $("#fileinput-button").prop("disabled",true);
                $("#btnSend").prop("disabled",true);
            }
        });

    </script>
</head>

<body class="sidebar-xs">

<!-- Main navbar -->
<div class="navbar navbar-default navbar-static-top header-highlight">


    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav">

            <li><a class="sidebar-control sidebar-secondary-hide hidden-xs"><i class="icon-transmission"></i></a></li>


        </ul>

        <div class="navbar-right">
            <p class="navbar-text">Hi, {{Auth::user()->name}}!</p>
            <p class="navbar-text"><span class="label bg-success">Online</span></p>
            <a class="navbar-text" href="{{ asset('logout') }}"
               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                Logout
            </a>

            <form id="logout-form" action="{{ asset('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>

            {{--<ul class="nav navbar-nav">--}}


                {{--<li class="dropdown">--}}
                    {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
                        {{--<i class="icon-bubble8"></i>--}}
                        {{--<span class="visible-xs-inline-block position-right">Messages</span>--}}
                        {{--<span class="status-mark border-pink-300"></span>--}}
                    {{--</a>--}}

                    {{--<div class="dropdown-menu dropdown-content width-350">--}}
                        {{--<div class="dropdown-content-heading">--}}
                            {{--Messages--}}
                            {{--<ul class="icons-list">--}}
                                {{--<li><a href="#"><i class="icon-compose"></i></a></li>--}}
                            {{--</ul>--}}
                        {{--</div>--}}

                        {{--<ul class="media-list dropdown-content-body">--}}





                            {{--<li class="media">--}}
                                {{--<div class="media-left">--}}
                                    {{--<img src="{{asset('assets/images/placeholder.jpg')}}" class="img-circle img-sm" alt="">--}}
                                    {{--<span class="badge bg-danger-400 media-badge">5</span>--}}
                                {{--</div>--}}

                                {{--<div class="media-body">--}}
                                    {{--<a href="#" class="media-heading">--}}
                                        {{--<span class="text-semibold">James Alexander</span>--}}
                                        {{--<span class="media-annotation pull-right">04:58</span>--}}
                                    {{--</a>--}}

                                    {{--<span class="text-muted">who knows, maybe that would be the best thing for me...</span>--}}
                                {{--</div>--}}
                            {{--</li>--}}

                            {{--<li class="media">--}}
                                {{--<div class="media-left">--}}
                                    {{--<img src="{{asset('assets/images/placeholder.jpg')}}" class="img-circle img-sm" alt="">--}}
                                    {{--<span class="badge bg-danger-400 media-badge">4</span>--}}
                                {{--</div>--}}

                                {{--<div class="media-body">--}}
                                    {{--<a href="#" class="media-heading">--}}
                                        {{--<span class="text-semibold">Margo Baker</span>--}}
                                        {{--<span class="media-annotation pull-right">12:16</span>--}}
                                    {{--</a>--}}

                                    {{--<span class="text-muted">That was something he was unable to do because...</span>--}}
                                {{--</div>--}}
                            {{--</li>--}}

                            {{--<li class="media">--}}
                                {{--<div class="media-left"><img src="assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>--}}
                                {{--<div class="media-body">--}}
                                    {{--<a href="#" class="media-heading">--}}
                                        {{--<span class="text-semibold">Jeremy Victorino</span>--}}
                                        {{--<span class="media-annotation pull-right">22:48</span>--}}
                                    {{--</a>--}}

                                    {{--<span class="text-muted">But that would be extremely strained and suspicious...</span>--}}
                                {{--</div>--}}
                            {{--</li>--}}

                            {{--<li class="media">--}}
                                {{--<div class="media-left"><img src="assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>--}}
                                {{--<div class="media-body">--}}
                                    {{--<a href="#" class="media-heading">--}}
                                        {{--<span class="text-semibold">Beatrix Diaz</span>--}}
                                        {{--<span class="media-annotation pull-right">Tue</span>--}}
                                    {{--</a>--}}

                                    {{--<span class="text-muted">What a strenuous career it is that I've chosen...</span>--}}
                                {{--</div>--}}
                            {{--</li>--}}

                            {{--<li class="media">--}}
                                {{--<div class="media-left"><img src="assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>--}}
                                {{--<div class="media-body">--}}
                                    {{--<a href="#" class="media-heading">--}}
                                        {{--<span class="text-semibold">Richard Vango</span>--}}
                                        {{--<span class="media-annotation pull-right">Mon</span>--}}
                                    {{--</a>--}}

                                    {{--<span class="text-muted">Other travelling salesmen live a life of luxury...</span>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                        {{--</ul>--}}

                        {{--<div class="dropdown-content-footer">--}}
                            {{--<a href="#" data-popup="tooltip" title="All messages"><i class="icon-menu display-block"></i></a>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</li>--}}
            {{--</ul>--}}
        </div>
    </div>
</div>
<!-- /main navbar -->


<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">


        <!-- /main sidebar -->


        <!-- Secondary sidebar -->
        <div class="sidebar sidebar-secondary sidebar-default">
            <div class="sidebar-content">



                <div class="sidebar-category">
                    <div class="category-title">
                        <span>Online users</span>
                        <ul class="icons-list">
                            <li><a href="#" data-action="collapse"></a></li>
                        </ul>
                    </div>

                    <div class="category-content no-padding">
                        <ul class="media-list media-list-linked" >






                            @yield('user_list')





                        </ul>
                    </div>
                </div>
                <!-- /online users -->


            </div>
        </div>
        <!-- /secondary sidebar -->


        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Page header -->
            <div class="page-header">
                <br>

                <div class="breadcrumb-line breadcrumb-line-component">
                    <ul class="breadcrumb">
                        <li><a href="#"><i class="icon-home2 position-left"></i> Home</a></li>
                        <li class="active"><a href="#">Conversation</a></li>

                    </ul>

                    <ul class="breadcrumb-elements">
                        <li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
                        {{--<li class="dropdown">--}}
                        {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
                        {{--<i class="icon-gear position-left"></i>--}}
                        {{--Settings--}}

                        {{--</a>--}}


                        {{--</li>--}}
                    </ul>
                </div>
            </div>
            <!-- /page header -->
            <br>


            <!-- Content area -->
            <div class="content">

                <!-- Basic layout -->
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h6 class="panel-title"><label id="user_name"></label></h6>

                    </div>

                    <div class="panel-body">
                        <ul class="media-list chat-list content-group" id="chat_area">



                            @yield('chat_box')






                        </ul>

                        <input type="hidden" id="user_id" value="{{Auth::user()->id}}">
                        <input type="hidden" id="to_user_id" value="">
                        <textarea name="enter-message" disabled class="form-control content-group message_input" rows="3" cols="1" placeholder="Enter your message..."></textarea>

                        <div class="row">
                            <div class="col-xs-6">
                                <ul class="icons-list icons-list-extended mt-10">
                                    {{--<li><a href="#" data-popup="tooltip" title="Send photo" data-container="body"><i class="icon-file-picture"></i></a></li>--}}
                                    {{--<li><a href="#" data-popup="tooltip" title="Send video" data-container="body"><i class="icon-file-video"></i></a></li>--}}
                                    <button type="button"  id="fileinput-button" data-popup="tooltip" title="Send File" class="btn btn-primary "><i class="icon-file-plus"></i> Send File</button>

                                </ul>
                            </div>

                            <div class="col-xs-6 text-right">
                                <button  onclick="sendMessage()" id="btnSend" type="button" class="btn bg-teal-400 btn-labeled btn-labeled-right"><b><i class="icon-circle-right2"></i></b> Send</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /basic layout -->


                <!-- Footer -->
                <div class="footer text-muted">
                    &copy; 2017. <a href="#">Chat Kun online</a>
                </div>
                <!-- /footer -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
<!-- /page container -->
<script>

    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#fileinput-button", {
        url: "{{url('chatkun/upload')}}",
        method:"POST",
        addRemoveLinks : true,

        previewTemplate:'<div style="display:none"></div>'
        ,addedfile: function(file) {
            var arrBuffer= file.name.split(".");
            var nameFile="";
            for(var index=0;index<arrBuffer.length-1;index++){
                nameFile=nameFile+""+arrBuffer[index];
            }
//            $("#song_name").val(nameFile);
//            $('#uploadProgress').css('width', '0%').attr('aria-valuenow',0);
//            $("#upload_progress").show();
//            $("#fileinput-button").attr('class','btn btn-primary');
//            $("#recordinput-button").attr('class','btn btn-default');
        },
        uploadprogress: function(file, progress, bytesSent) {
//            $('#uploadProgress').css('width', progress+'%').attr('aria-valuenow',progress);
//            $('#uploadProgress').html(progress+'%');
        },
        success: function (file, response) {
            create_message_timeline(file.name,'left','file',response.link);
            console.log(response);

        },  error: function (file, response) {
            console.log(response);
        }, sending: function(file, xhr, formData) {
            // Pass token. You can use the same method to pass any other values as well such as a id to associate the image with for example.
            formData.append("_token",'{{csrf_token()}}');
           formData.append("to_user_id",to_user_id);
           // formData.append("_token",'');


        }

    });
</script>
@yield('script')
</body>
</html>
