<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $group
 */
#[Fillable(['key', 'value', 'group'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;
}
