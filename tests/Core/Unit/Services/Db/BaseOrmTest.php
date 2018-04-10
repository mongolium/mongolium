<?php

namespace Tests\Core\Unit\Services\Db;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Services\Db\BaseOrm;
use Mongolium\Core\Services\Db\Client;
use MongoDB\BSON\ObjectId;
use Tests\Core\Helper\Admin;
use Mongolium\Core\Model\Admin as AdminModel;
use Mongolium\Core\Services\Db\BaseModel;
use Mongolium\Core\Services\Db\Hydrator;
use Mockery as m;

class BaseOrmTest extends TestCase
{
    public function testMakeObjectId()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['id'] = '5ac18d1100000132000ABc00';

        $result = $baseOrm->makeObjectId($data);

        $this->assertInstanceOf(ObjectId::class, $result['_id']);
        $this->assertFalse(isset($result['id']));
    }

    public function testMakeObjectIdComplexArray()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['id'] = '5ac18d1100043132000ABc00';
        $data['name'] = 'john';

        $result = $baseOrm->makeObjectId($data);

        $this->assertInstanceOf(ObjectId::class, $result['_id']);
        $this->assertFalse(isset($result['id']));
        $this->assertEquals('john', $data['name']);
    }

    public function testMakeObjectIdNoId()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['name'] = 'rob';

        $result = $baseOrm->makeObjectId($data);

        $this->assertEquals('rob', $data['name']);
        $this->assertFalse(isset($result['id']));
        $this->assertFalse(isset($result['_id']));
    }

    public function testEmptyEntityId()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['id'] = '123';

        $result = $baseOrm->emptyEntityId($data);

        $this->assertEquals('', $result['id']);
    }

    public function testEmptyEntityIdNoId()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['name'] = 'chris';

        $result = $baseOrm->emptyEntityId($data);

        $this->assertEquals('', $result['id']);
        $this->assertEquals('chris', $result['name']);
    }

    public function testHasId()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $admin = AdminModel::hydrate(Admin::admin(true));

        $this->assertTrue($baseOrm->hasId($admin));
    }

    public function testHasIdFalse()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $adminHelper = Admin::admin(true);
        $adminHelper['id'] = '';

        $admin = AdminModel::hydrate($adminHelper);

        $this->assertFalse($baseOrm->hasId($admin));
    }

    public function testGetEntityProperties()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $keys = $baseOrm->getEntityProperties(AdminModel::class);

        $this->assertEquals($keys[0], 'id');
        $this->assertEquals($keys[1], 'username');
        $this->assertEquals($keys[2], 'password');
        $this->assertEquals($keys[3], 'email');
        $this->assertEquals($keys[4], 'firstName');
        $this->assertEquals($keys[5], 'lastName');
        $this->assertEquals($keys[6], 'type');
        $this->assertEquals($keys[7], 'createdAt');
        $this->assertEquals($keys[8], 'updatedAt');
    }

    /**
     * @expectedException Mongolium\Core\Exceptions\OrmException
     * @expectedExceptionMessage Invalid Model Tests\Core\Unit\Services\Db\TestModel constructor has no parameters.
     */
    public function testGetEntityPropertiesFail()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $keys = $baseOrm->getEntityProperties(TestModel::class);
    }

    public function testEntityHasData()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['id'] = '123';
        $data['username'] = 'james';
        $data['password'] = 'world';
        $data['email'] = 'james@test.com';
        $data['first_name'] = 'james';
        $data['last_name'] = 'world';
        $data['type'] = 'admin';
        $data['created_at'] = '2018-09-01 12:12:12';
        $data['updated_at'] = '2018-09-01 12:12:12';

        $result = $baseOrm->entityHasData(AdminModel::class, $data);

        $this->assertTrue($result);
    }

    /**
     * @expectedException Mongolium\Core\Exceptions\OrmException
     */
    public function testEntityHasBadData()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['car'] = '123';
        $data['park'] = 'james';

        $baseOrm->entityHasData(AdminModel::class, $data);
    }

    /**
     * @expectedException Mongolium\Core\Exceptions\OrmException
     */
    public function testEntityHasDataIncompleteData()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['id'] = '123';
        $data['username'] = 'james';
        $data['password'] = 'world';
        $data['email'] = 'james@test.com';
        $data['first'] = 'James';

        $baseOrm->entityHasData(AdminModel::class, $data);
    }

    public function testGetDataKeys()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data = $baseOrm->getDataKeys([
            'firstName' => 'josh',
            'last_name' => 'smith'
        ]);

        $this->assertFalse(in_array('last_name', $data));
        $this->assertTrue(in_array('lastName', $data));
        $this->assertTrue(in_array('firstName', $data));
    }

    public function testGetDataKeysDoubleDash()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data = $baseOrm->getDataKeys([
            'firstName' => 'josh',
            'last__name' => 'smith'
        ]);

        $this->assertFalse(in_array('last_name', $data));
        $this->assertTrue(in_array('lastName', $data));
        $this->assertTrue(in_array('firstName', $data));
    }

    public function testGetDataKeysMultiDash()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data = $baseOrm->getDataKeys([
            'firstName' => 'josh',
            'last_name_again_and_again' => 'smith'
        ]);

        $this->assertFalse(in_array('last_name', $data));
        $this->assertTrue(in_array('lastNameAgainAndAgain', $data));
        $this->assertTrue(in_array('firstName', $data));
    }

    public function testKeyPartsToKey()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $string = $baseOrm->keyPartsToKey([
            'my',
            'first',
            'name'
        ]);

        $this->assertEquals('myFirstName', $string);
    }

    public function testKeyPartsToKeyOddData()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $string = $baseOrm->keyPartsToKey([
            'My',
            'first',
            'Name'
        ]);

        $this->assertEquals('myFirstName', $string);
    }

    public function testKeyPartsToKeyOddDataTwo()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $string = $baseOrm->keyPartsToKey([
            'My',
            'first',
            '',
            'Name'
        ]);

        $this->assertEquals('myFirstName', $string);
    }

    public function testMakeEntityArray()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data = [
            'car',
            'park',
            ['foo', 'bar']
        ];

        $result = $baseOrm->makeEntityArray($data);

        $this->assertEquals('car', $result[0]);
        $this->assertEquals('park', $result[1]);
        $this->assertEquals('foo', $result[2][0]);
        $this->assertEquals('bar', $result[2][1]);
    }

    public function testMakeEntityArrayWithBSON()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $bsonArray = new \MongoDB\Model\BSONArray;
        $bsonArray->bsonUnserialize(['foo', 'bar']);

        $data = [
            'car',
            'park',
            $bsonArray
        ];

        $result = $baseOrm->makeEntityArray($data);

        $this->assertEquals('car', $result[0]);
        $this->assertEquals('park', $result[1]);
        $this->assertEquals('foo', $result[2][0]);
        $this->assertEquals('bar', $result[2][1]);
    }

    public function testMakeEntityId()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['id'] = '5ac18d1100000132000abc00';
        $data['name'] = 'Rob';
        $data['age'] = 20;

        $result = $baseOrm->makeObjectId($data);

        $document = new \MongoDB\Model\BSONDocument;
        $document->bsonUnserialize($result);

        $array = $baseOrm->makeEntityId($document);

        $this->assertEquals('5ac18d1100000132000abc00', $array['id']);
        $this->assertEquals('Rob', $array['name']);
        $this->assertEquals(20, $array['age']);
    }

    public function testMakeEntityNoId()
    {
        $baseOrm = m::mock(BaseOrm::class)->makePartial();

        $data['name'] = 'Rob';
        $data['age'] = 20;

        $document = new \MongoDB\Model\BSONDocument;
        $document->bsonUnserialize($data);

        $array = $baseOrm->makeEntityId($document);

        $this->assertEquals('Rob', $array['name']);
        $this->assertEquals(20, $array['age']);
    }
}

class TestModel extends BaseModel
{
    protected function __construct()
    {
    }

    public static function hydrate(array $data): Hydrator
    {
        return new self(
        );
    }

    public function extract(): array
    {
        return [
        ];
    }
}
