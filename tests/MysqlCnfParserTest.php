<?php

use PHPUnit\Framework\TestCase;
use druidfi\GdprDump\MysqlCnfParser;

class MysqlCnfParserTest extends TestCase
{
    /**
     * @covers MysqlCnfParser::parse
     */
    public function testParseCnfWithoutIncludes()
    {
        $output = MysqlCnfParser::parse(__DIR__ . "/assets/cnfWithoutIncludes.cnf");
        $this->assertTrue(is_array($output));
        $this->assertArrayHasKey("client", $output);
    }

    /**
     * @covers MysqlCnfParser::parse
     */
    public function testParseCndWithIncludeFile()
    {
        $output = MysqlCnfParser::parse(__DIR__ . "/assets/cnfWithIncludeFile.cnf");
        $this->assertArrayHasKey("included", $output);
    }

    /**
     * @covers MysqlCnfParser::parse
     */
    public function testParseCndWithIncludeDirectory()
    {
        $output = MysqlCnfParser::parse(__DIR__ . "/assets/cnfIncludesDirectory.cnf");
        $this->assertArrayHasKey("included", $output);
    }

    /**
     * @covers MysqlCnfParser::parse
     */
    public function testParseCndWithIncludeDirectoryWithMultipleFiles()
    {
        $output = MysqlCnfParser::parse(__DIR__ . "/assets/cnfIncludesDirectory.cnf");
        $this->assertArrayHasKey("included", $output);
        $this->assertArrayHasKey("includedmore", $output["included"]);
        $this->assertArrayHasKey("includedisini", $output["included"]);
    }

    /**
     * @covers MysqlCnfParser::parse
     */
    public function testDealWithSelfReferentialIncludes()
    {
        $output = MysqlCnfParser::parse(__DIR__ . "/assets/cnfInfiniteInclude.cnf");
        $this->assertArrayHasKey("infinite", $output);
    }
}
