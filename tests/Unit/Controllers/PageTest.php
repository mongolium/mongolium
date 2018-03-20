<?php

namespace Tests\Unit\Controllers;

use Mongolium\Controllers\Page;
use Mongolium\Services\Page as PageService;
use Mongolium\Model\Page as PageModel;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use PHPUnit\Framework\TestCase;
use Tests\Helper\Page as PageHelper;
use Mockery as m;

class PageTest extends TestCase
{
    public function testPage()
    {
        $pageService = m::mock(PageService::class);

        $page = new Page($pageService);

        $this->assertInstanceOf(Page::class, $page);
    }

    public function testGetPage()
    {
        $page = PageHelper::page(true);

        $pageService = m::mock(PageService::class);
        $pageService->shouldReceive('getPage')->once()->andReturn(
            PageModel::hydrate($page)
        );

        $pageController = new Page($pageService);

        $request = m::mock(Request::class);

        $response = new SlimResponse(200);

        $result = $pageController->readOne($request, $response, ['id' => '123abc']);

        $this->assertInstanceOf(SlimResponse::class, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"' . $page['id'] . '"|', $data);
        $this->assertRegExp('|"title":"' . $page['title'] . '"|', $data);
        $this->assertRegExp('|"slug":"' . $page['slug'] . '"|', $data);
    }
}
