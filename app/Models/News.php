<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    public static $rules = [
        'onCreate' => [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ],
        'onList' => [
            'page'          => 'required|integer',
            'limit'         => 'required|integer',
            'column'        => 'string',
            'sort'          => 'string',
        ],
        'onUpdate' => [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ],
    ];

    public static $messages = [
        'required' => 'The :attribute field is required.',
        'integer' => 'The :attribute field must be an integer.',
        'date' => 'The :attribute field must be a date format.',
    ];

    protected $hidden = [];

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comments', 'news_id')->where(function ($query) {
            $query->where('deleted_at', '')
                  ->orWhere('deleted_at', NULL)
                  ->orWhere('deleted_at', '0000-00-00 00:00:00');
        });
    }
}
