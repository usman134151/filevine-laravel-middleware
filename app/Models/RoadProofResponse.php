<?php

namespace App\Models;

use App\Models\UserAPIList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoadProofResponse extends Model
{
    use HasFactory;
    protected $guarded = [];   
    
    



    public function user_api_list()
    {
        return $this->belongsTo(UserAPIList::class,'user_id','id');
    }
}
