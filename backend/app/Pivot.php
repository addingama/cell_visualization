<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pivot extends Model
{
    protected $table = 'pivot';
    protected $primaryKey = 'MSISDN';
    public $timestamps = false;
}
