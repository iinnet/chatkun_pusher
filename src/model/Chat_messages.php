<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat_messages extends Model
{
    protected $fillable = [
        'user_id', 'to_user_id','message','status','type','option'
    ];
    public function getOptionAttribute(){
        return  url('/chatkun/download')."/".$this->id.'/'.md5($this->id.base64_encode($this->id));
    }

}
