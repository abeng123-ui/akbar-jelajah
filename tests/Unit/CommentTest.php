<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\Constant;
use App\Http\Controllers\API\UserController;

class CommentTest extends TestCase
{
     use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    /** @test */
    public function it_create_comment() {
        $user = $this->create_user();
        $params = $this->params();
        $params_news = $this->params_news();
        $create_news = $this->create_news($params_news);

        $params['user_id'] = $user->id;
        $params['news_id'] = $create_news->id;

        $data = \App\Traits\CommentsTrait::createComments($params);

        $this->assertEquals(1, count($data), "it_create_comment test failed");
    }

    /** @test */
    public function it_find_many_comment() {
        $pagination = $this->pagination();
        $user = $this->create_user();
        $params = $this->params();
        $params_news = $this->params_news();
        $create_news = $this->create_news($params_news);

        $params['user_id'] = $user->id;
        $params['news_id'] = $create_news->id;

        $create = $this->create_comments($params);

        $data = \App\Traits\CommentsTrait::findManyComments($pagination);

        $this->assertEquals(2, count($data), "it_find_many_comment test failed");
    }

    /** @test */
    public function it_find_one_comment() {
        $pagination = $this->pagination();
        $user = $this->create_user();
        $params = $this->params();
        $params_news = $this->params_news();
        $create_news = $this->create_news($params_news);

        $params['user_id'] = $user->id;
        $params['news_id'] = $create_news->id;

        $create = $this->create_comments($params);

        $data = \App\Traits\CommentsTrait::findOneCommentsById($create->id, $params['user_id']);

        $this->assertEquals(1, count($data), "it_find_one_comment test failed");
    }

    /** @test */
    public function it_update_comment() {
        $pagination = $this->pagination();
        $params_update = $this->params_update();
        $user = $this->create_user();
        $params = $this->params();
        $params_news = $this->params_news();
        $create_news = $this->create_news($params_news);

        $params['user_id'] = $user->id;
        $params['news_id'] = $create_news->id;

        $create = $this->create_comments($params);

        $find = \App\Traits\CommentsTrait::findOneCommentsById($create->id, $params['user_id']);

        $params_update['user_id'] = $user->id;
        $params_update['news_id'] = $create_news->id;

        $data = \App\Traits\CommentsTrait::updateComments($find, $params_update);

        $this->assertEquals("this is comment 2", $data->comment, "it_update_comment test failed") ;
    }

    /** @test */
    public function it_delete_one_comment_by_id() {
        $user = $this->create_user();
        $params = $this->params();
        $params_news = $this->params_news();
        $create_news = $this->create_news($params_news);

        $params['user_id'] = $user->id;
        $params['news_id'] = $create_news->id;

        $create = $this->create_comments($params);

        $find = \App\Traits\CommentsTrait::findOneCommentsById($create->id, $params['user_id']);

        $data = \App\Traits\CommentsTrait::deleteComments($find);

        $this->assertNotEquals('', $data->deleted_at, "it_delete_one_comment_by_id test failed");
    }

    public function pagination(){
        return [
            "page" => 1,
            "limit" => 10,
            "column" => "id",
            "sort" => "desc",
        ];
    }

    public function params(){
        return [
            'comment' => 'this is comment',
        ];
    }

    public function params_update(){
        return [
            'comment' => 'this is comment 2',
        ];
    }

    public function create_user(){

        return $user = \App\User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'role' => Constant::ROLE_USER,
            'password' => bcrypt('123456'),
        ]);
    }

    public function params_news(){
        return [
            'title' => 'this is title',
            'description' => 'this is description',
            'image' => 'this is image',
        ];
    }

    public function create_comments($params)
    {
        $data = new \App\Models\Comments();
        $data->user_id = $params['user_id'];
        $data->news_id = $params['news_id'];
        $data->comment = $params['comment'];
        $data->save();

        return $data;
    }

    public function create_news($params)
    {
        $data = new \App\Models\News();
        $data->title = $params['title'];
        $data->image = $params['image'];
        $data->description = $params['description'];
        $data->save();

        return $data;
    }
}
