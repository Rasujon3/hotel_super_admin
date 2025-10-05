<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    use HasFactory;

    protected $table = 'property_types';
    protected $fillable = [
        'name',
        'image_url',
        'image_path',
        'status',
    ];

    protected $hidden = [
        'image_path',
        'created_at',
        'updated_at',
    ];

    public static function rules()
    {
        return [
            'name' => ['required', 'string', 'max:45', 'unique:property_types,name'],
            'status' => 'required|in:Active,Inactive',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
}
