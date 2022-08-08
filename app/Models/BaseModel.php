<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Filters;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

/**
 * 模型基类
 * Class BaseModel
 * @package App\Models
 * Author Ripper. 2022/4/8
 */
class BaseModel extends Model
{
    use SoftDeletes, Filters;

    protected static $softDelete = true;

    /**
     * 自定义时间格式
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
        'deleted_at' => 'date:Y-m-d H:i:s',
    ];

    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return Carbon::instance($date)->toDateTimeString();
    }

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeletes()
    {
        if (static::$softDelete === true) {
            static::addGlobalScope(new SoftDeletingScope);
        }
    }

    public function addAll(array $data)
    {
        $rs = DB::table($this->getTable())->insert($data);
        return $rs;
    }

    public function updateOrCreateAll(array $data)
    {
        foreach ($data as $v) {
            $res = self::updateOrCreate($v[array_key_first($v)], $v[array_key_last($v)]);
        }

        return $res;
    }

    public function scopeWithOnly($query, $relation, array $columns)
    {
        return $query->with([$relation => function ($query) use ($columns) {
            $query->select(array_merge(['id'], $columns));
        }]);
    }

    public function _sql()
    {
        DB::listen(function ($query) {
            $bindings = $query->bindings;
            $sql = $query->sql;
            foreach ($bindings as $replace) {
                $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                $sql = preg_replace('/\?/', $value, $sql, 1);
            }
            dump($sql);
        });
    }

    /**
     * 自动设置数据表字段为过滤字段
     * @return array
     */
    public function getFillable()
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        unset($columns['id']);
        return $columns;
    }
}
