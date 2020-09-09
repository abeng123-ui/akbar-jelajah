<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Transformers\CommentsListTransformer;

class NewsListTransformer extends TransformerAbstract {

    public function transform($datas) {

        foreach ($datas as $data) {
            $data->id = (int) $data->id;
            $data->title = (string) $data->title;
            $data->image = (string) $data->image;
            $data->description = (string) $data->description;
            $data->created_at = (string) $data->created_at;
            $data->updated_at = (string) $data->updated_at;
            $data->deleted_at = (string) $data->deleted_at;

            $transform = new CommentsListTransformer();
            $data->comments = $transform->transform($data->comments);
        }

        return $datas;
    }

}

?>
