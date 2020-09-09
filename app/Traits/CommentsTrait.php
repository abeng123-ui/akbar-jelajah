<?php

namespace App\Traits;

use App\Models\Comments;

trait CommentsTrait
{
    public static function createComments($params)
    {
        $data = new Comments();
        $data->user_id = $params['user_id'];
        $data->news_id = $params['news_id'];
        $data->comment = $params['comment'];
        $data->save();

        return $data;
    }

    public static function findManyComments($params) {
        $news = Comments::where(function ($query) {
            $query->where('deleted_at', '')
                  ->orWhere('deleted_at', NULL)
                  ->orWhere('deleted_at', '0000-00-00 00:00:00');
        });

        if(isset($params['column'])){
            $getColumns = new Comments;
            $columns = $getColumns->getTableColumns();

            if(in_array($params['column'], $columns) && isset($params['sort']) && $params['sort'] != ''){
                if($params['sort'] == "desc"){
                    $news = $news->orderBy($params['column'], 'desc');
                }else{
                    $news = $news->orderBy($params['column'], 'asc');
                }
            }
        }

        $result['length'] = $news->count();

        if (isset($params['page']) && isset($params['limit']) && $params['page'] > 0 && $params['limit'] > 0) {
            $news->limit($params['limit'])
            ->skip(($params['page'] - 1) * $params['limit']);
        }

        $result['data'] = $news->get();

        return $result;
    }

    public static function findOneCommentsById($id, $user_id) {
        $news = Comments::where('id', $id)
        ->where('user_id', $user_id)
        ->where(function ($query) {
            $query->where('deleted_at', '')
                  ->orWhere('deleted_at', NULL)
                  ->orWhere('deleted_at', '0000-00-00 00:00:00');
        })
        ->first();

        return $news;
    }

    public static function updateComments($data, $params) {
        $data->comment = isset($params['comment']) ? $params['comment'] : $data->comment;

        $data->save();
        return $data;
    }

    public static function deleteComments($news) {
        $news->deleted_at = \Carbon\Carbon::now();

        $news->save();
        return $news;
    }

}
