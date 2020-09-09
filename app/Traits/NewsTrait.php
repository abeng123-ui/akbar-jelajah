<?php

namespace App\Traits;

use App\Models\News;

trait NewsTrait
{
    public static function createNews($params)
    {
        $data = new News();
        $data->title = $params['title'];
        $data->image = $params['image'];
        $data->description = $params['description'];
        $data->save();

        return $data;
    }

    public static function findManyNews($params) {
        $news = News::with('comments')
        ->where(function ($query) {
            $query->where('deleted_at', '')
                  ->orWhere('deleted_at', NULL)
                  ->orWhere('deleted_at', '0000-00-00 00:00:00');
        });

        if(isset($params['column'])){
            $getColumns = new News;
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

    public static function findOneNewsById($id) {
        $news = News::where('id', $id)
        ->where(function ($query) {
            $query->where('deleted_at', '')
                  ->orWhere('deleted_at', NULL)
                  ->orWhere('deleted_at', '0000-00-00 00:00:00');
        })
        ->first();

        return $news;
    }

    public static function findOneNewsByTitle($title='') {
        $data = News::where(function ($query) {
                $query->where('deleted_at', '')
                      ->orWhere('deleted_at', NULL)
                      ->orWhere('deleted_at', '0000-00-00 00:00:00');
            })
            ->where('title', $title)
            ->first();

        return $data;
    }

    public static function updateNews($data, $params) {
        $data->title = isset($params['title']) ? $params['title'] : $data->title;
        $data->image = isset($params['image']) ? $params['image'] : $data->image;
        $data->description = isset($params['description']) ? $params['description'] : $data->description;

        $data->save();
        return $data;
    }

    public static function deleteNews($news) {
        $news->deleted_at = \Carbon\Carbon::now();

        $news->save();
        return $news;
    }

}
