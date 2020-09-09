<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\CommentsTrait;
use App\Traits\NewsTrait;
use App\Models\Comments;
use App\Helpers\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use DB;
use App\Transformers\CommentsListTransformer;

class CommentsController extends Controller
{
    use CommentsTrait, NewsTrait;

    public function create(Request $request) {
        DB::BeginTransaction();
        try {
            $params   = $request->all();
            $userData = Auth::user();
            $params['user_id'] = $userData->id;

            $validator = Validator::make($params, Comments::$rules['onCreate'], Comments::$messages);

            if ($validator->fails()) {
                $message = implode(" | ",$validator->errors()->all());
                return response()->json(['error' => true, 'message' => $message], Constant::ERROR);
            }

            if($userData->role != Constant::ROLE_USER){
                return response()->json(['error' => true, 'message' => 'Unauthorized'], Constant::UNAUTHORIZED);
            }

            $news = $this->findOneNewsById($params['news_id']);
            if (!$news) {
                return response()->json(['error' => true, 'message' => 'News not found'], Constant::ERROR);
            }

            $data = $this->createComments($params);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("{$e->getMessage()}\n{$e->getTraceAsString()}");
            return response()->json(['error' => true, 'message' => "Exception Error"], Constant::EXCEPTION);
        }

        return response()->json(['error' => false, 'message' => 'Successfully create', 'data' => $data], Constant::SUCCESS);

    }

    public function list(Request $request) {
        try {
            $params    = $request->all();

            $validator = Validator::make($params, Comments::$rules['onList'], Comments::$messages);

            if ($validator->fails()) {
                $message = implode(" | ",$validator->errors()->all());
                return response()->json(['error' => true, 'message' => $message], Constant::ERROR);
            }

            $comment = $this->findManyComments($params);

            $transform = new CommentsListTransformer();
            $comment['data'] = $transform->transform($comment['data']);

        } catch (\Exception $e) {
            Log::error("{$e->getMessage()}\n{$e->getTraceAsString()}");
            return response()->json(['error' => true, 'message' => "Exception Error"], Constant::EXCEPTION);
        }

        return response()->json(
            ['error'   => false,
             'message' => 'Successfully get list',
             'data'    => $comment['data'],
             'length'  => $comment['length']
        ] , Constant::SUCCESS);

    }

    public function update(Request $request, $comment_id) {
        DB::BeginTransaction();
        try {
            $request['id'] = $comment_id;
            $params    = $request->all();
            $userData  = Auth::user();

            $validator = Validator::make($params, Comments::$rules['onUpdate'], Comments::$messages);

            if ($validator->fails()) {
                $message = implode(" | ",$validator->errors()->all());
                return response()->json(['error' => true, 'message' => $message], Constant::ERROR);
            }

            if($userData->role != Constant::ROLE_USER){
                return response()->json(['error' => true, 'message' => 'Unauthorized'], Constant::UNAUTHORIZED);
            }

            $comment = $this->findOneCommentsById($params['id'], $userData->id);
            if (!$comment) {
                return response()->json(['error' => true, 'message' => 'Comments not found'], Constant::ERROR);
            }

            $news = $this->findOneNewsById($params['news_id']);
            if (!$news) {
                return response()->json(['error' => true, 'message' => 'News not found'], Constant::ERROR);
            }

            $comment = $this->updateComments($comment, $params);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("{$e->getMessage()}\n{$e->getTraceAsString()}");
            return response()->json(['error' => true, 'message' => "Exception Error"], Constant::EXCEPTION);
        }

        return response()->json(
            ['error'   => false,
             'message' => 'Successfully update'
        ] , Constant::SUCCESS);

    }

    public function delete(Request $request, $comment_id) {
        DB::BeginTransaction();
        try {
            $request['id'] = $comment_id;
            $params    = $request->all();
            $userData  = Auth::user();

            if($userData->role != Constant::ROLE_USER){
                return response()->json(['error' => true, 'message' => 'Unauthorized'], Constant::UNAUTHORIZED);
            }

            $comment = $this->findOneCommentsById($comment_id, $userData->id);
            if (!$comment) {
                return response()->json(['error' => true, 'message' => 'Comments not found'], Constant::ERROR);
            }

            $comment = $this->deleteComments($comment);

            DB::commit();
        } catch (\Exception $e) {
            Log::error("{$e->getMessage()}\n{$e->getTraceAsString()}");
            DB::rollback();
            return response()->json(['error' => true, 'message' => "Exception Error"], Constant::EXCEPTION);
        }

        return response()->json(
            ['error'   => false,
             'message' => 'Successfully delete'
        ] , Constant::SUCCESS);


    }
}
