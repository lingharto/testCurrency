<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        self::saved(function ($model) {
            $model->log();
        });
    }

    private function log()
    {
        $arr = ['char_code' => $this->char_code, 'name' => $this->name, 'rate' => $this->rate];
        DB::table('currency_history')->insert($arr);
    }
}
