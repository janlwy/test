<?php
use PHPUnit\Framework\TestCase;
use App\Models\Manager;
use App\Models\TexteRepository;
use App\Models\Texte;

class TexteRepositoryTest extends TestCase
{
    private $repository;
    private $managerMock;
    
    protected function setUp(): void
    {
        $this->managerMock = $this->createMock(Manager::class);
        $this->repository = new TexteRepository();
        $this->repository->manager = $this->managerMock;
    }

    public function testFindById()
    {
        $expectedData = [
            'id' => 1,
            'title' => 'Test Title',
            'content' => 'Test Content'
        ];

        $this->managerMock->expects($this->once())
                         ->method('readTableOne')
                         ->with('texte', 1)
                         ->willReturn($expectedData);

        $result = $this->repository->findById(1);
        $this->assertEquals($expectedData, $result);
    }

    public function testFindAllByUser()
    {
        $expectedData = [
            [
                'id' => 1,
                'title' => 'Test 1',
                'content' => 'Content 1',
                'user_id' => 1
            ],
            [
                'id' => 2,
                'title' => 'Test 2',
                'content' => 'Content 2',
                'user_id' => 1
            ]
        ];

        $this->managerMock->expects($this->once())
                         ->method('readTableAll')
                         ->with('texte', 1)
                         ->willReturn($expectedData);

        $result = $this->repository->findAllByUser(1);
        $this->assertEquals($expectedData, $result);
    }

    public function testSave()
    {
        $texte = new Texte([
            'id' => 1,
            'title' => 'Test Title',
            'content' => 'Test Content'
        ]);

        $this->managerMock->expects($this->once())
                         ->method('updateTable')
                         ->with('texte', $texte->toArray(), 1);

        $this->repository->save($texte);
    }

    public function testDelete()
    {
        $this->managerMock->expects($this->once())
                         ->method('deleteTable')
                         ->with('texte', 1);

        $this->repository->delete(1);
    }

    public function testCount()
    {
        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->method('fetchColumn')
                        ->willReturn(5);

        $this->managerMock->expects($this->once())
                         ->method('getConnexion')
                         ->willReturn($this->createMock(PDO::class));

        $count = $this->repository->count();
        $this->assertEquals(5, $count);
    }
}
