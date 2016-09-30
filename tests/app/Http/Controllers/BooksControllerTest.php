<?php
namespace Tests\App\Http\Controllers;

use Laravel\Lumen\Testing\DatabaseTransactions;
use TestCase;

class BooksControllerTest extends TestCase
{
    /** @test **/
    public function indexStatusCodeShouldBe200()
    {
        $response = $this->call('GET', '/books');

        $this->assertEquals(200, $response->status());
    }

    /** @test **/
    public function index_should_return_a_collection_of_records()
    {
        $this->json('GET', '/books')
            ->seeJson([
                'title' => 'War of the Worlds',
            ])
            ->seeJson([
                'title' => 'A Wrinkle in Time',
            ]);
    }

    /** @test **/
    public function show_should_return_a_valid_book()
    {
        $this
            ->json('GET', '/books/1')
            ->seeStatusCode(200)
            ->seeJson([
                'id' => 1,
                'title' => 'War of the Worlds',
                'description' => 'A science fiction masterpiece about Martians invading London',
                'author' => 'H. G. Wells'
            ]);
        $data = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
    }

    /** @test **/
    public function show_should_fail_when_the_book_id_does_not_exist()
    {
        $this
            ->get('/books/99999')
            ->seeStatusCode(404)
            ->seeJson([
                'error' => [
                    'message' => 'Book not found'
                ]
            ]);
    }

    /** @test **/
    public function show_route_should_not_match_an_invalid_route()
    {
        $this->get('/books/this-is-invalid');
        $this->assertNotRegExp(
            '/Book not found/',
            $this->response->getContent(),
            'BooksController@show route matching when it should not.'
        );
    }
}