<?php

namespace App\Imports;

use AlicFeng\IdentityCard\Application\IdentityCard;
use App\Exceptions\ImportValidateException;
use App\Models\Blacklist;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class BlacklistsImport implements ToCollection
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
                Blacklist::updateOrCreate([
                    'id_card' => $format['id_card']
                ], Arr::except($format, ['id_card']));
            } catch (ImportValidateException $exception) {
                $this->pushError($row, $exception->getMessage());
                return;
            } catch (\Exception $exception) {
                \Log::error('导入黑名单异常:' . $exception->getMessage());
                $this->pushError($row, '导入异常，请联系系统管理员处理');
                return;
            }
        });
    }

    public function format($row)
    {
        $data['name'] = $row[0];
        $data['id_card'] = $this->validateIdCard($row[1]);
        $data['gender'] = (new IdentityCard())->sex($data['id_card']) == 'M' ? '男' : '女';
        $data['phone'] = $row[2];
        $data['reason'] = $row[3];
        return $data;
    }

    protected function validateIdCard($idCard)
    {
        throw_unless($idCard, new ImportValidateException('身份证号不能为空'));
        throw_unless((new IdentityCard())->validate($idCard), new ImportValidateException('身份证号规则错误'));
        $idCard = Str::upper($idCard);
        return $idCard;
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
        return ['姓名', '身份证号', '手机号', '拉黑原因'];
    }

    protected function pushError($row, $message = ''): array
    {
        $error = $row->toArray();
        array_push($error, $message);
        array_push($this->errors, $error);
        return $error;
    }
}
