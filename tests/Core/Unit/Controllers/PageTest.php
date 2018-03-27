<?php

namespace Tests\Core\Unit\Controllers;

use Mongolium\Core\Controllers\Page;
use Mongolium\Core\Services\Page as PageService;
use Mongolium\Core\Model\Page as PageModel;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use PHPUnit\Framework\TestCase;
use Tests\Core\Helper\Page as PageHelper;
use ReallySimple\Collection;
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

    public function testGetPages()
    {
        $pages[] = PageModel::hydrate(PageHelper::page(true));
        $pages[] = PageModel::hydrate(PageHelper::page(true));
        $pages[] = PageModel::hydrate(PageHelper::page(true));
        $pages[] = PageModel::hydrate(PageHelper::page(true));

        $pageService = m::mock(PageService::class);
        $pageService->shouldReceive('getPublished')->once()->andReturn(
            new Collection($pages)
        );

        $pageController = new Page($pageService);

        $request = m::mock(Request::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);

        $response = new SlimResponse(200);

        $result = $pageController->read($request, $response);

        $this->assertInstanceOf(SlimResponse::class, $result);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"|', $data);
        $this->assertRegExp('|"type":"page"|', $data);
        $this->assertRegExp('|"data":\[|', $data);
        $this->assertRegExp('|"links":{|', $data);
    }
}
