<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CommentsListTransformer extends TransformerAbstract {

    public function transform($datas) {

        foreach ($datas as $data) {
            $data->id = (int) $data->id;
            $data->user_id = (int) $data->user_id;
            $data->news_id = (int) $data->news_id;
            $data->comment = (string) $data->comment;
            $data->created_at = (string) $data->created_at;
            $data->updated_at = (string) $data->updated_at;
            $data->deleted_at = (string) $data->deleted_at;
        }

        return $datas;
    }

}

?>
