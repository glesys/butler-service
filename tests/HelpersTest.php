<?php

namespace Butler\Service\Tests;

class HelpersTest extends TestCase
{
    public function test_sqlite_database_path()
    {
        $this->assertEquals(database_path('path'), sqlite_database_path('path', 'sqlite'));
        $this->assertEquals('path', sqlite_database_path('path', 'mysql'));
    }

    public function test_is_grahpql()
    {
        $this->assertTrue(is_graphql('{ foo { bar } }'));
        $this->assertTrue(is_graphql('query { foo { bar } }'));
        $this->assertTrue(is_graphql(<<<'EOD'
            query getFoo($bar: [String!]) {
                getFoo(bar: $bar) {
                    baz
                }
            }
            EOD
        ));

        $this->assertFalse(is_graphql('kuery { baz }'));
        $this->assertFalse(is_graphql('query { baz'));
    }
}
