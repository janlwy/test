<?php
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private $validator;
    
    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testRequiredRule()
    {
        $data = ['field' => ''];
        $rules = ['field' => [['rule' => 'required']]];
        
        $this->assertFalse($this->validator->validate($data, $rules));
        $this->assertNotEmpty($this->validator->getErrors());
    }

    public function testEmailRule()
    {
        $data = ['email' => 'invalid-email'];
        $rules = ['email' => [['rule' => 'email']]];
        
        $this->assertFalse($this->validator->validate($data, $rules));
        
        $data['email'] = 'valid@email.com';
        $this->assertTrue($this->validator->validate($data, $rules));
    }

    public function testMinMaxRules()
    {
        $data = ['field' => 'a'];
        $rules = ['field' => [
            ['rule' => ['min', 2]],
            ['rule' => ['max', 5]]
        ]];
        
        $this->assertFalse($this->validator->validate($data, $rules));
        
        $data['field'] = 'valid';
        $this->assertTrue($this->validator->validate($data, $rules));
        
        $data['field'] = 'too-long-value';
        $this->assertFalse($this->validator->validate($data, $rules));
    }

    public function testPatternRule()
    {
        $data = ['username' => 'user@123'];
        $rules = ['username' => [
            ['rule' => ['pattern', '/^[a-zA-Z0-9_-]{3,20}$/']]
        ]];
        
        $this->assertTrue($this->validator->validate($data, $rules));
        
        $data['username'] = 'invalid@username';
        $this->assertFalse($this->validator->validate($data, $rules));
    }

    public function testCustomMessage()
    {
        $customMessage = 'Custom error message';
        $data = ['field' => ''];
        $rules = ['field' => [
            ['rule' => 'required', 'message' => $customMessage]
        ]];
        
        $this->validator->validate($data, $rules);
        $errors = $this->validator->getErrors();
        
        $this->assertEquals($customMessage, $errors['field'][0]);
    }

    public function testMultipleRules()
    {
        $data = ['password' => 'weak'];
        $rules = ['password' => [
            ['rule' => 'required'],
            ['rule' => ['min', 8]],
            ['rule' => ['pattern', '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/']]
        ]];
        
        $this->assertFalse($this->validator->validate($data, $rules));
        $this->assertCount(2, $this->validator->getErrors()['password']);
    }
}
