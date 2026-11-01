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
            ->with('text', 1, 'array')
            ->willReturn($expectedData);

        $result = $this->repository->findById(1);
        $this->assertInstanceOf(Texte::class, $result);
        $this->assertEquals('Test Title', $result->getTitle());
    }

    public function testFindAllByUser()
    {
        $row1 = [
            'id' => 1,
            'title' => 'Test 1',
            'content' => 'Content 1',
            'user_id' => 1
        ];
        $row2 = [
            'id' => 2,
            'title' => 'Test 2',
            'content' => 'Content 2',
            'user_id' => 1
        ];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchAll')->willReturn([$row1, $row2]);
        $stmtMock->method('fetch')->willReturnOnConsecutiveCalls($row1, $row2, false);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $this->managerMock->expects($this->once())
            ->method('getConnexion')
            ->willReturn($pdoMock);

        $result = $this->repository->findAllByUser(1);
        $this->assertCount(2, $result);
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
            ->with('text', $this->anything(), 1)
            ->willReturn(true);

        $this->repository->save($texte);
    }

    public function testDelete()
    {
        $this->managerMock->expects($this->once())
            ->method('deleteTable')
            ->with('text', 1)
            ->willReturn(true);

        $this->repository->delete(1);
    }

    public function testCount()
    {
        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->method('fetchColumn')
            ->willReturn(5);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('query')->willReturn($pdoStatementMock);
        $pdoMock->method('prepare')->willReturn($pdoStatementMock);

        $this->managerMock->expects($this->once())
            ->method('getConnexion')
            ->willReturn($pdoMock);

        $count = $this->repository->count();
        $this->assertEquals(5, $count);
    }
}
