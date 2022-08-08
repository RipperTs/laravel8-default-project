<?php

namespace App\Http\Services;

use App\Http\Enums\BalanceType;
use App\Models\Customer;
use App\Models\CustomerBalanceLog;

/**
 * 用户余额变动明细服务
 * Class CustomerBalanceLogService
 * @package App\Http\Services
 * Author Ripper. 2022/7/1
 */
class CustomerBalanceLogService
{


    /**
     * 获取明细列表
     * @param $data
     */
    public function getList($data)
    {
        $paginate = $data['per_page'] ?? 15;
        $list_type = $data['list_type'] ?? 'default';
        $list = CustomerBalanceLog::query()
            ->where('pro_id', $data['pro_id'])
            ->where('cid', $data['cid'])
            ->orderBy('id', 'desc')
            ->orderBy('after_balance', 'asc')
            ->paginate($paginate)->toArray();
        if (!count($list['data'])) {
            $list['data'] = [];
        }
        if (count($list['data']) && $list_type == 'timeline') {
            $list['data'] = $this->dataGroup($list['data'], 'years');
        }
        return $list;
    }

    /**
     * @description:根据数据
     * @param {dataArr:需要分组的数据；keyStr:分组依据}
     * @return:
     */
    protected function dataGroup(array $dataArr, string $keyStr): array
    {
        $newArr = [];
        foreach ($dataArr as $k => $val) {    //数据根据日期分组
            $newArr[$val[$keyStr]][] = $val;
        }
        arsort($newArr);
        reset($newArr);
        foreach ($newArr as $k => $v) {
            $new_array[] = [
                'years' => $k,
                'list' => $newArr[$k],
            ];
        }
        return $new_array;
    }

    /**
     * 新增余额变动明细 (需在更新用户余额之前调用)
     * @param $scene
     * @param $data
     * @param $describeParam
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public static function add($user_id, $scene, $data, $describeParam = [])
    {
        // 查看变动前金额
        $beforeBalance = Customer::query()->where('id', $user_id)->value('balance');
        if ($data['flow_type'] == 10) {
            $afterBalance = sprintf('%.2f', $beforeBalance + $data['money']);
        } else {
            $afterBalance = sprintf('%.2f', $beforeBalance - $data['money']);
        }
        $model = new CustomerBalanceLog();
        return $model->query()->create(array_merge([
            'cid' => $user_id,
            'scene' => $scene,
            'before_balance' => $beforeBalance,
            'after_balance' => $afterBalance,
            'describe' => vsprintf(BalanceType::data()[$scene]['describe'], $describeParam),
        ], $data));
    }

}
