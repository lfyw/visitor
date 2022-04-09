<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function syncIssue($idCard)
    {
        $issues = Issue::whereIdCard($idCard)->get();
        //如果没有下发记录，不做任何变动调整
        if ($issues->first()){
            return ;
        }
        //如果没有下发错误，说明全部正确
        $issuesAllSuccess = !$issues->where('issue_status', false)->first();
        $issuesAllFail = !$issues->where('issue_status', true)->first();
        if ($issuesAllSuccess){

        }elseif($issuesAllFail){

        }else{

        }
    }

}
