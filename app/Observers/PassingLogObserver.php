<?php

namespace App\Observers;

use AlicFeng\IdentityCard\Application\IdentityCard;
use App\Enums\GateRule;
use App\Models\PassingLog;
use App\Models\Scene;
use App\Models\Visitor;

class PassingLogObserver
{
    public function creating(PassingLog $passingLog)
    {
        $passingLog->name = $this->getName($passingLog);
        $passingLog->type = $this->getType($passingLog);
        $passingLog->gender = $this->getGender($passingLog);
        $passingLog->age = $this->getAge($passingLog);
        $passingLog->phone = $this->getPhone($passingLog);
        $passingLog->unit = $passingLog->visitor->unit;
        $passingLog->reason = $passingLog->visitor->reason;
        $passingLog->relation = $passingLog->visitor->relation;
        $passingLog->user_department = $this->getUserDepartment($passingLog);
        $passingLog->user_name = $this->getUserName($passingLog);
    }

    public function created(PassingLog $passingLog)
    {
        $passingLogCount = PassingLog::whereIdCard($passingLog->id_card)->count();

        $visitor = Visitor::whereIdCard($passingLog->id_card)->first();
        //更新访客访问次数
        $visitor->fill(['access_count' => $passingLogCount])->save();
        //记录现场人员
        $gate = $passingLog->gate;
        $passageway = $passingLog->gate->passageways()->first();
        $way = $passageway->ways()->first();

        if ($way->name == '生产区'){
            if ($gate->rule == GateRule::IN->value) {
                Scene::in($visitor->id, $way->id, $passingLog->gate_id, $passageway->id);
            } else {
                Scene::out($visitor->id, $passageway->id);
            }
        }
    }


    protected function getUserDepartment($passingLog)
    {
        $userDepartment = '';
        if ($this->isTemporary($passingLog)) {
            $department = $passingLog->visitor->user->department;
            if ($parent = $department?->ancestors->first()) {
                $userDepartment = $parent->name . '-' . $department->name;
            } else {
                $userDepartment = $department?->name;
            }
        }
        return $userDepartment;
    }

    protected function getUserName($passingLog)
    {
        return $this->isTemporary($passingLog) ? $passingLog->visitor->user->real_name : null;
    }

    protected function getPhone($passingLog)
    {
        if ($this->isTemporary($passingLog)) {
            $phone = $passingLog->visitor->phone;
        } else {
            $phone = $passingLog->visitor->userAsVisitor->phone_number;
        }
        return $phone;
    }

    protected function getAge($passingLog)
    {
        if ($this->isTemporary($passingLog)) {
            $age = $passingLog->visitor->age;
        } else {
            $age = (new IdentityCard())->age($passingLog->visitor->userAsVisitor->id_card);
        }
        return $age;
    }

    protected function getGender($passingLog)
    {
        if ($this->isTemporary($passingLog)) {
            $gender = $passingLog->visitor->gender;
        } else {
            $gender = (new IdentityCard())->sex($passingLog->visitor->userAsVisitor->id_card) == 'M' ? '男' : '女';
        }
        return $gender;
    }

    protected function getType($passingLog)
    {
        if ($this->isTemporary($passingLog)) {
            $type = $passingLog->visitor->visitorType->name;
        } else {
            $type = $passingLog->visitor->userAsVisitor->userType->name;
        }
        return $type;
    }

    protected function getName($passingLog)
    {
        if ($this->isTemporary($passingLog)) {
            return $passingLog->visitor->name;
        }
        return $passingLog->visitor->userAsVisitor->real_name;
    }

    protected function isTemporary($passingLog)
    {
        return $passingLog->visitor->type == Visitor::TEMPORARY;
    }
}
