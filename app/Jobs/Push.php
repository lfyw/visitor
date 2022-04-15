<?php

namespace App\Jobs;

use AlicFeng\IdentityCard\Application\IdentityCard;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Push implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $idCard)
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->visitorHasIdCard()) {

        }
    }

    protected function findVisitor()
    {
        if ($visitor = Visitor::firstWhere('id_card', $this->idCard)) {
            return $visitor;
        }
        if ($user = User::firstWhere('id_card', $this->idCard)) {

            $department = $user->department;
            if ($parent = $department->ancestors->first()) {
                $userDepartment = $parent->name . '-' . $department->name;
            } else {
                $userDepartment = $department->name;
            }

            $visitor = Visitor::create([
                'name' => $user->real_name,
                'visitor_type_id' => 0,
                'id_card' => $user->id_card,
                'gender' => (new IdentityCard())->sex($user->id_card) == 'M' ? '男' : '女',
                'age' => (new IdentityCard())->age($user->id_card),
                'phone' => $user->phone_number,
                'unit' => $userDepartment,
            ]);
        }
    }

}
