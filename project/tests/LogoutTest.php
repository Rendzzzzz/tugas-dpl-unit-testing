<?php

use PHPUnit\Framework\TestCase;

define('PHPUNIT_RUNNING', true);

require_once __DIR__ . '/../logout.php';

class LogoutTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user'] = 'admin';
    }

    public function testSessionDestroyed()
    {
        logoutUser();

        $this->assertEmpty($_SESSION);
    }

    public function testRedirectLocation()
    {
        $redirect = logoutUser();

        $this->assertEquals('login.php', $redirect);
    }
}