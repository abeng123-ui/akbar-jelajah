<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\NewsTrait;
use App\Models\News;
use App\Helpers\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use DB;
use App\Transformers\NewsListTransformer;

class NewsController extends Controller
{
    use NewsTrait;

    public function create(Request $request) {
        DB::BeginTransaction();
        try {
            $params   = $request->all();
            $userData = Auth::user();

            $validator = Validator::make($params, News::$rules['onCreate'], News::$messages);

            if ($validator->fails()) {
                $message = implode(" | ",$validator->errors()->all());
                return response()->json(['error' => true, 'message' => $message], Constant::ERROR);
            }

            if($userData->role != Constant::ROLE_ADMIN){
                return response()->json(['error' => true, 'message' => 'Unauthorized'], Constant::UNAUTHORIZED);
            }

            $checkNews = $this->findOneNewsByTitle($params['title']);
            if ($checkNews) {
                return response()->json(['error' => true, 'message' => "News with that title already exists"], Constant::SUCCESS);
            }

            $data = $this->createNews($params);

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

            $validator = Validator::make($params, News::$rules['onList'], News::$messages);

            if ($validator->fails()) {
                $message = implode(" | ",$validator->errors()->all());
                return response()->json(['error' => true, 'message' => $message], Constant::ERROR);
            }

            $news = $this->findManyNews($params);

            $transform = new NewsListTransformer();
            $news['data'] = $transform->transform($news['data']);

        } catch (\Exception $e) {
            Log::error("{$e->getMessage()}\n{$e->getTraceAsString()}");
            return response()->json(['error' => true, 'message' => "Exception Error"], Constant::EXCEPTION);
        }

        return response()->json(
            ['error'   => false,
             'message' => 'Successfully get list',
             'data'    => $news['data'],
             'length'  => $news['length']
        ] , Constant::SUCCESS);

    }

    public function update(Request $request, $news_id) {
        DB::BeginTransaction();
        try {
            $request['id'] = $news_id;
            $params    = $request->all();
            $userData  = Auth::user();

            $validator = Validator::make($params, News::$rules['onUpdate'], News::$messages);

            if ($validator->fails()) {
                $message = implode(" | ",$validator->errors()->all());
                return response()->json(['error' => true, 'message' => $message], Constant::ERROR);
            }

            if($userData->role != Constant::ROLE_ADMIN){
                return response()->json(['error' => true, 'message' => 'Unauthorized'], Constant::UNAUTHORIZED);
            }

            $news = $this->findOneNewsById($params['id']);
            if (!$news) {
                return response()->json(['error' => true, 'message' => 'News not found'], Constant::ERROR);
            }

            $news = $this->updateNews($news, $params);

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

    public function delete(Request $request, $news_id) {
        DB::BeginTransaction();
        try {
            $request['id'] = $news_id;
            $params    = $request->all();
            $userData  = Auth::user();

            if($userData->role != Constant::ROLE_ADMIN){
                return response()->json(['error' => true, 'message' => 'Unauthorized'], Constant::UNAUTHORIZED);
            }

            $news = $this->findOneNewsById($news_id);
            if (!$news) {
                return response()->json(['error' => true, 'message' => 'News not found'], Constant::ERROR);
            }

            $news = $this->deleteNews($news);

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
