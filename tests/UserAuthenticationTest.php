<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class UserAuthenticationTest extends TestCase
{
    private $http;

    protected function setUp(): void
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost:1880/api/v3/']);
    }

    protected function tearDown(): void
    {
        $this->http = null;
    }

    public function testLauncherLogin()
    {
        $userInfo = $this->http->get('launcher_login.php?username=testUser&password=Antonios12!');
        $this->assertEquals(200, $userInfo->getStatusCode());
        $body = json_decode($userInfo->getBody()->getContents(), true);

        $this->assertTrue($body['success']);
        
        $this->assertEquals('testUser', $body['username']);
        $this->assertEquals('52qfulsp9trigsb4', $body['accessToken']);
    }
}