<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $table = 'comments';

    public static $rules = [
        'onCreate' => [
            'news_id' => 'required|integer',
            'comment' => 'required|string|max:255',
        ],
        'onList' => [
            'page'          => 'required|integer',
            'limit'         => 'required|integer',
            'column'        => 'string',
            'sort'          => 'string',
        ],
        'onUpdate' => [
            'comment' => 'required|string|max:255',
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
}
