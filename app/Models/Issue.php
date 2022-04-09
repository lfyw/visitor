<?php

namespace App\Models;

use App\Enums\IssueStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function syncIssue($idCard):void
    {
        $issues = Issue::whereIdCard($idCard)->get();
        //如果没有下发记录，不做任何变动调整
        if ($issues->first()){
            return ;
        }
        //如果没有下发错误，说明全部正确；如果没有下发正确，说明全部错误；否则部分成功
        $visitor = Visitor::firstWhere(['id_card' => $idCard]);
        $user = User::firstWhere(['id_card' => $idCard]);
        $issuesAllSuccess = !$issues->where('issue_status', false)->first();
        $issuesAllFail = !$issues->where('issue_status', true)->first();

        if ($issuesAllSuccess){
            $visitor?->fill(['issue_status' => IssueStatus::SUCCESS])->save();
            $user?->fill(['issue_status' => IssueStatus::SUCCESS])->save();
        }elseif($issuesAllFail){
            $visitor?->fill(['issue_status' => IssueStatus::FAILURE])->save();
            $user?->fill(['issue_status' => IssueStatus::FAILURE])->save();
        }else{
            $visitor?->fill(['issue_status' => IssueStatus::PARTIAL_SUCCESS])->save();
            $user?->fill(['issue_status' => IssueStatus::PARTIAL_SUCCESS])->save();
        }
    }

}
