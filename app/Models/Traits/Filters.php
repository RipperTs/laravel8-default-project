<?php


namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Filters
{
    /**
     * @param Builder $query
     * @param $attributes
     * @return Builder
     */
    public function scopeWithFilters($query, $attributes)
    {
        $class = $this;
        collect($attributes)->each(function ($attr, $field) use ($query, $class) {
            if (!is_null($attr)) {
                // 使用驼峰的形式将方法名动态写出来
                $scopeFilter = Str::camel("{$field} filter");
                $scopeFilterFunc = Str::camel("scope {$scopeFilter}");
                if (method_exists($class, $scopeFilterFunc)) {
                    $query->{$scopeFilter}($attr);
                }
            }
        });

        return $query;
    }
}
