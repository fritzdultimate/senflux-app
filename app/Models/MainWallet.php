<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class MainWallet extends Model {
    protected $guarded = [];

    protected function casts() {
        return [
            'is_active' => 'boolean'
        ];
    }
}