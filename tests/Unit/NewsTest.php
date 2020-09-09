<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\Constant;
use Illuminate\Support\Facades\Auth;

class NewsTest extends TestCase
{
     use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    /** @test */
    public function it_create_news() {
        $params = $this->params();

        $data = \App\Traits\NewsTrait::createNews($params);

        $this->assertEquals(1, count($data), "it_create_news test failed");
    }

    /** @test */
    public function it_find_many_news() {
        $pagination = $this->pagination();
        $params = $this->params_update();

        $create = $this->create_news($params);

        $data = \App\Traits\NewsTrait::findManyNews($pagination);

        $this->assertEquals(2, count($data), "it_find_many_news test failed");
    }

    /** @test */
    public function it_find_one_news() {
        $pagination = $this->pagination();
        $params = $this->params_update();

        $create = $this->create_news($params);

        $data = \App\Traits\NewsTrait::findOneNewsById($create->id);

        $this->assertEquals(1, count($data), "it_find_one_news test failed");
    }

    /** @test */
    public function it_find_one_news_by_title() {
        $pagination = $this->pagination();
        $params = $this->params_update();

        $create = $this->create_news($params);

        $data = \App\Traits\NewsTrait::findOneNewsByTitle($create->title);

        $this->assertEquals(1, count($data), "it_find_one_news_by_title test failed");
    }

    /** @test */
    public function it_update_news() {
        $pagination = $this->pagination();
        $params = $this->params();
        $params_update = $this->params_update();

        $create = $this->create_news($params);

        $find = \App\Traits\NewsTrait::findOneNewsById($create->id);

        $data = \App\Traits\NewsTrait::updateNews($find, $params_update);

        $this->assertEquals("this is title 2", $data->title, "it_update_news test failed") ;
    }

    /** @test */
    public function it_delete_one_news_by_id() {
        $params = $this->params();

        $create = $this->create_news($params);

        $find = \App\Traits\NewsTrait::findOneNewsById($create->id);

        $data = \App\Traits\NewsTrait::deleteNews($find);

        $this->assertNotEquals('', $data->deleted_at, "it_delete_one_news_by_id test failed");
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
            'title' => 'this is title',
            'description' => 'this is description',
            'image' => 'this is image',
        ];
    }

    public function params_update(){
        return [
            'title' => 'this is title 2',
            'description' => 'this is description 2',
            'image' => 'this is image 2',
        ];
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
