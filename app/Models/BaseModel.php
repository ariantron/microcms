<?php

namespace App\Models;

use App\Traits\HasUuidV7Trait;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasUuidV7Trait;

    public $incrementing = false;
    protected $keyType = 'string';
}
