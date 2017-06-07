<?php

namespace App\Http\Controllers;

use App\Chat_messages;
use App\ChatUserManagement;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Pusher;

class ChatkunController extends Controller
{

    public $options;
    public $pusher;
    function __construct() {
        $this->options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );
        $this->pusher = new Pusher(
            '71baa59665e9ba7ac6e9',
            '8efbc5d481a62566ace8',
            '344751',
            $this->options
        );

    }


    public function countOnlineUser(){

        $user=Auth::user();

        $userOnline=ChatUserManagement::where('user_id',$user->id);

        if($userOnline->count()==0){
            ChatUserManagement::create(['user_id'=>Auth::user()->id,'resource_id'=>'online']);
        }else{
            ChatUserManagement::where('user_id',Auth::user()->id)->delete();
            ChatUserManagement::create(['user_id'=>Auth::user()->id,'resource_id'=>'online']);
        }

        // Clear User out of online
        DB::select("SET sql_mode=''");
        DB::select("DELETE FROM chat_user_managements WHERE chat_user_managements.created_at> DATE_ADD(NOW(),INTERVAL 30 SECOND)");
    }
    public function index(){
        $this->countOnlineUser();
        $user_id=  Input::get('user_id');
        $name=  Input::get('name');
        /** Get User List  */
        DB::select("SET sql_mode=''");
        $user=Auth::user();
        $userData=  DB::select("SELECT
                                    users.*,
                                    if(chat_user_managements.resource_id is null,0,1) as online
                                    FROM users
                                    LEFT JOIN  chat_user_managements ON chat_user_managements.user_id=users.id
                                    WHERE users.id!= ".$user->id ."  GROUP BY users.id");
        return view('chatkun.chat_page',compact('userData','user_id','name'));
    }

    public function viewHistory($to_user_id){
        $user_id=Auth::user()->id;
        $history_result= Chat_messages::where("user_id",$user_id)->where("to_user_id",$to_user_id)->orwhere("user_id",$to_user_id)->where("to_user_id",$user_id)->get();
        return $history_result;

    }
    public function uploadFile(Request $request){
        if ($file = $request->file('file')) {
            $original_name = $file->getClientOriginalName();
            $extensionFile = $file->getClientOriginalExtension();
            $resultPaht = $request->file->store('myFile');

            /////// Send File ////////////////////
            $to_user_id = Input::get('to_user_id');
            $message = Input::get('message');

            $result= Chat_messages::create(['user_id'=>Auth::user()->id,'to_user_id'=>$to_user_id,'message'=>$message,'status'=>'unread','type'=>'file','option'=>$resultPaht]);
            $data['message'] = $message;
            $data['type']="file";
            $data['link']=url('/chatkun/download')."/".$result->id.'/'.md5($result->id.base64_encode($result->id));
            $data['from']=Auth::user()->id;
            $data['name']=Auth::user()->name;
            $this->pusher->trigger($to_user_id, 'my-event', $data);
            return  $data;
        }
    }
    public function pushMessage(){

        $to_user_id = Input::get('to_user_id');
        $message = Input::get('message');
        $result= Chat_messages::create(['user_id'=>Auth::user()->id,'to_user_id'=>$to_user_id,'message'=>$message,'status'=>'unread','type'=>'message','option'=>'']);

        $data['message'] = $message;
        $data['type']="message";
        $data['from']=Auth::user()->id;
        $data['name']=Auth::user()->name;
        $data['link']=url('/chatkun/download')."/".$result->id.'/'.md5($result->id.base64_encode($result->id));
        $this->pusher->trigger($to_user_id, 'my-event', $data);
    }

    public function download($chat_message_id,$token){


        if(md5($chat_message_id.base64_encode($chat_message_id))==$token){

            DB::select("SET sql_mode=''");
            $user=Auth::user();
            $chat_message=  DB::select("SELECT * FROM chat_messages
                                    WHERE chat_messages.id=$chat_message_id
                                    AND (chat_messages.user_id=".$user->id." OR chat_messages.to_user_id=".$user->id." ) ");

            $file=  storage_path().'/app/'.$chat_message[0]->option;
            $sp=explode('.',$chat_message[0]->option);

            $extFile=$sp[count($sp)-1];

            $headers = array(
                'Content-Type: application/'.$extFile,
            );

            return Response::download($file, 'ChatKun-Download-When_'.date('Y-m-d').'.'.$extFile, $headers);

        }

    }
}
