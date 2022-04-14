<?php

namespace App\Imports;

use AlicFeng\IdentityCard\Application\IdentityCard;
use App\Exceptions\ImportValidateException;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\Way;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class VisitorsImport implements ToCollection
{
    use Importable;

    protected $errors = [];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $rows = $collection->skip(3);
        $this->setRowsCount($rows->count());

        $rows->each(function ($row) {
            try {
                $format = $this->format($row);
                $visitor = Visitor::create($format['formatRow']);
                $visitor->ways()->sync($format['wayIds']);
            } catch (ImportValidateException $exception) {
                $this->formatNumberToString($row);
                $this->pushError($row, $exception->getMessage());
                return;
            } catch (\Exception $exception) {
                $this->formatNumberToString($row);
                \Log::error('导入访客异常:' . $exception->getMessage());
                $this->pushError($row, '导入异常，请联系系统管理员处理');
                return;
            }
        });
    }

    protected function format($row)
    {
        $formatRow['name'] = $this->validateName($row[0]);
        $formatRow['visitor_type_id'] = $this->validateVisitorTypeId($row[1]);
        $formatRow['id_card'] = $this->validateIdCard($row[2]);
        $formatRow['gender'] = (new IdentityCard())->sex($formatRow['id_card']) == 'M' ? '男' : '女';
        $formatRow['age'] = (new IdentityCard())->age($formatRow['id_card']);
        $formatRow['phone'] = $this->validatePhone($row[3]);
        $formatRow['unit'] = $row[4];
        $formatRow['reason'] = $row[5];
        $formatRow['user_id'] = $this->validateUserId($row[6]);
        $formatRow['relation'] = $row[7];
        $formatRow['limiter'] = $this->validateLimiter($row[8]);
        $formatRow['access_date_from'] = $this->validateAccessDateFrom($row[9]);
        $formatRow['access_date_to'] = $this->validateAccessDateTo($row[10], $formatRow['access_date_from']);
        $formatRow['access_time_from'] = $row[11];
        $formatRow['access_time_to'] = $row[12];
        $formatRow['type'] = Visitor::TEMPORARY;
        $wayIds = $this->validateWayIds($row[13]);
        return compact('formatRow', 'wayIds');
    }

    protected function formatNumberToString($row)
    {
        $row[2] = "'" . $row[2];
        $row[3] = "'" . $row[3];
        $row[6] = "'" . $row[6];
        return $row;
    }

    protected function validateWayIds($ways)
    {
        $wayIds = [];
        $ways = explode('，', $ways);
        foreach ($ways as $way) {
            throw_unless($wayModel = Way::firstWhere('name', $way), new ImportValidateException('路线名称在系统中不存在'));
            array_push($wayIds, $wayModel->id);
        }
        return array_values(array_unique($wayIds));
    }

    protected function validateAccessDateTo($accessDateTo, $accessDateFrom)
    {
        throw_unless($accessDateTo, new ImportValidateException('截止访问日期不能为空'));
        try {
            $parseFromAccessDateTo = Carbon::parse($accessDateFrom);
        } catch (\Exception $exception) {
            throw new ImportValidateException('截止访问日期格式需要按照以下格式：2022-4-14');
        }
        return $parseFromAccessDateTo;
    }

    protected function validateAccessDateFrom($accessDateFrom)
    {
        throw_unless($accessDateFrom, new ImportValidateException('起始访问日期不能为空'));
        try {
            $parse = Carbon::parse($accessDateFrom);
        } catch (\Exception $exception) {
            throw new ImportValidateException('起始访问日期格式需要按照以下格式：2022-4-14');
        }
        return $parse;
    }

    protected function validateLimiter($limiter)
    {
        if (!$limiter) {
            return $limiter;
        }
        throw_unless(preg_match("/\d+/", intval($limiter)), new ImportValidateException('访问次数只能是数字'));
        return $limiter;
    }

    protected function validateUserId($userIdCard)
    {
        throw_unless($userIdCard, new ImportValidateException('被访问人身份证号不能为空'));
        throw_unless((new IdentityCard())->validate($userIdCard), new ImportValidateException('被访问人身份证号规则错误'));
        $userIdCard = Str::upper($userIdCard);
        throw_unless($user = User::firstWhere('id_card', $userIdCard), new ImportValidateException('被访问人在系统中查不到'));
        return $user->id;
    }

    protected function validatePhone($phone)
    {
        throw_unless($phone, new ImportValidateException('手机号不能为空'));
        return $phone;
    }

    protected function validateIdCard($idCard)
    {
        throw_unless($idCard, new ImportValidateException('身份证号不能为空'));
        throw_unless((new IdentityCard())->validate($idCard), new ImportValidateException('身份证号规则错误'));
        $idCard = Str::upper($idCard);
        throw_if(Visitor::firstWhere('id_card', $idCard), new ImportValidateException('身份证号已存在系统中'));
        return $idCard;
    }

    protected function validateVisitorTypeId($visitorType)
    {
        throw_unless($visitorType, new ImportValidateException('访客类型不能为空'));
        throw_unless($visitorTypeModel = VisitorType::firstWhere('name', $visitorType), new ImportValidateException('访客类型在系统中不存在'));
        return $visitorTypeModel->id;
    }

    protected function validateName($name)
    {
        throw_unless($name, new ImportValidateException('姓名不能为空'));
        return $name;
    }

    protected function setRowsCount($count)
    {
        $this->rowsCount = $count;
    }

    public function getRowsCount(): int
    {
        return $this->rowsCount;
    }

    public function getErrorsCount(): int
    {
        return count($this->errors) ?: 0;
    }

    public function getErrorsWithHeader(): array
    {
        $header = $this->getHeaders();
        return $header ? array_merge([$header], $this->errors) : $this->errors;
    }


    protected function getHeaders(): array
    {
        return ['姓名', '访客类型', '身份证号', '手机号', '所属单位', '访问事由', '被访问人身份证号', '与被访问人关系', '访问次数', '起始访问日期', '截止访问日期', '起始访问时间', '截止访问时间', '访问路线'];
    }

    protected function pushError($row, $message = ''): array
    {
        $error = $row->toArray();
        array_push($error, $message);
        array_push($this->errors, $error);
        return $error;
    }
}
