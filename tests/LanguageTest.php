<?php


class LanguageTest extends TestCase
{
    public function testLanguage()
    {
        $response = $this->call('GET', '/language');
        $this->assertEquals(200, $response->status());
    }
}
