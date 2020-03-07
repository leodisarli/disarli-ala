<?php

namespace App\Repositories;

use Illuminate\Database\DatabaseManager;
use Mockery;
use PHPUnit\Framework\TestCase;
use Uuid\Uuid;

class BaseRepositoryTest extends TestCase
{
    /**
     * @covers \App\Repositories\BaseRepository::__construct
     */
    public function testCreateBaseRepository()
    {
        $dbSpy = Mockery::spy(DatabaseManager::class);
        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbSpy,
                $uuidSpy
            ]
        );
        $this->assertInstanceOf(
            BaseRepository::class,
            $baseRepository
        );
    }

    /**
     * @covers \App\Repositories\BaseRepository::getById
     */
    public function testGetById()
    {
        $return = (object) [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($return)
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getById = $baseRepository->getById(1);

        $this->assertEquals($return, $getById);
        $this->assertEquals($return->id, 'id');
        $this->assertEquals($return->name, 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getDeadById
     */
    public function testGetDeadById()
    {
        $return = (object) [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($return)
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getById = $baseRepository->getDeadById(1);

        $this->assertEquals($return, $getById);
        $this->assertEquals($return->id, 'id');
        $this->assertEquals($return->name, 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetList()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getDeadList
     */
    public function testGetDeadList()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getById = $baseRepository->getDeadList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::insert
     */
    public function testInsert()
    {
        $id = '123456';

        $data = [
            'id' => $id,
            'name' => 'teste',
            'created' => date('Y-m-d H:i:d'),
            'modified' => date('Y-m-d H:i:d'),
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('insert')
            ->with($data)
            ->andReturnSelf()
            ->getMock();

        $uuidMock = Mockery::mock(Uuid::class)
            ->shouldReceive('uuid4')
            ->withNoArgs()
            ->andReturn($id)
            ->getMock();

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidMock
            ]
        );

        $insert = $baseRepository->insert($data);

        $this->assertEquals($insert, $id);
    }

    /**
     * @covers \App\Repositories\BaseRepository::update
     */
    public function testUpdate()
    {
        $id = '123456';

        $data = [
            'name' => 'teste',
            'modified' => date('Y-m-d H:i:d'),
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('id', $id)
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('update')
            ->with($data)
            ->andReturnSelf()
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $update = $baseRepository->update($data, $id);

        $this->assertEquals($update, true);
    }

    /**
     * @covers \App\Repositories\BaseRepository::delete
     */
    public function testDelete()
    {
        $id = '123456';

        $data = [
            'modified' => date('Y-m-d H:i:d'),
            'deleted' => date('Y-m-d H:i:d'),
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('id', $id)
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('update')
            ->with($data)
            ->andReturnSelf()
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $delete = $baseRepository->delete($id);

        $this->assertEquals($delete, true);
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListWithLike()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('user_name', 'like', '%dim%')
            ->andReturnSelf()
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            [
                'user_name' => [
                    'type' => 'lik',
                    'data' => 'dim'
                ]
            ],
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListWithEqual()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('user_name', '=', 'dim')
            ->andReturnSelf()
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            [
                'user_name' => [
                    'type' => 'eql',
                    'data' => 'dim'
                ]
            ],
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListWithNull()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('user_name')
            ->andReturnSelf()
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            [
                'user_name' => [
                    'type' => 'nul'
                ]
            ],
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListWithNotNull()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('user_name')
            ->andReturnSelf()
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            [
                'user_name' => [
                    'type' => 'nnu'
                ]
            ],
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getBulk
     */
    public function testGetBulk()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('whereIn')
            ->with('id', [1, 2])
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->andReturnSelf()
            ->shouldReceive('appends')
            ->with([])
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->getMock();

        $uuidSpy = Mockery::spy(Uuid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $uuidSpy
            ]
        );

        $getBulk = $baseRepository->getBulk(
            [
                1,
                2
            ],
            [
                'id'
            ],
            'id',
            'desc',
            []
        );

        $this->assertEquals($return, $getBulk);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
