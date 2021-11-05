<?php

namespace MirazMac\Requirements;

/**
 * Tests for the requirements checker
 *
 * Note: We are using phpunit 4 for this because we need to support PHP 5.4 >=
 */
class CheckerTest extends \PHPUnit_Framework_TestCase
{
    protected $checker;

    public function setUp()
    {
        $this->checker = new Checker;
    }

    public function tearDown()
    {
        $this->checker = null;
    }

    public function testOSFail()
    {
        $this->checker->resetRequirements();

        if (DIRECTORY_SEPARATOR === '/') {
            $os = $this->checker::OS_DOS;
        } else {
            $os = $this->checker::OS_UNIX;
        }

        $this->checker
             ->requireOS($os)
             ->check();

        $this->assertEquals(false, $this->checker->isSatisfied());
    }

    public function testOSPass()
    {
        $this->checker->resetRequirements();

        if (DIRECTORY_SEPARATOR === '/') {
            $os = $this->checker::OS_UNIX;
        } else {
            $os = $this->checker::OS_DOS;
        }

        $this->checker
             ->requireOS($os)
             ->check();

        $this->assertEquals(true, $this->checker->isSatisfied());
    }

    public function testFunctionsPass()
    {
        $this->checker->resetRequirements();

        $this->checker
             ->requireFunctions(['sprintf', 'strtolower', 'ucfirst|ucwords'])
             ->check();

        $this->assertEquals(true, $this->checker->isSatisfied());
    }

    public function testFunctionsFail()
    {
        $this->checker->resetRequirements();

        $this->checker
             ->requireFunctions([rand() . uniqid(), uniqid() . rand()])
             ->check();

        $this->assertEquals(false, $this->checker->isSatisfied());
    }

    public function testClassesPass()
    {
        $this->checker->resetRequirements();

        $this->checker
             ->requireClasses(['Directory', 'Exception|RuntimeException'])
             ->check();

        $this->assertEquals(true, $this->checker->isSatisfied());
    }

    public function testClassesFail()
    {
        $this->checker->resetRequirements();

        $this->checker
             ->requireFunctions([rand() . uniqid(), uniqid() . rand()])
             ->check();

        $this->assertEquals(false, $this->checker->isSatisfied());
    }

    public function testUnlimitedIniValuePass()
    {
        ini_set('memory_limit', -1);

        $this->checker->resetRequirements();
        $this->checker->requireIniValues([
            'memory_limit' => '>=64M',
        ]);

        $this->assertEquals(true, $this->checker->isSatisfied());
    }


    /**
     * @dataProvider phpVersionPassData
     */
    public function testPHPVersionPass($version)
    {
        $this->checker->resetRequirements();

        $this->checker
             ->requirePhpVersion($version)
             ->check();

        $this->assertEquals(true, $this->checker->isSatisfied());
    }

    /**
     * @dataProvider phpVersionFailData
     */
    public function testPHPVersionFail($version)
    {
        $this->checker->resetRequirements();

        $this->checker
             ->requirePhpVersion($version)
             ->check();

        $this->assertEquals(false, $this->checker->isSatisfied());
    }

    public function testIniPass()
    {
        $this->checker->resetRequirements();

        $setting = ini_get('file_uploads');
        if ($setting == 'On' || $setting == '1') {
            $setting = true;
        } elseif ($setting == 'Off' || $setting == '' || $setting == '0') {
            $setting = false;
        }

        $this->checker
        ->requireIniValues([
            'file_uploads' => $setting
        ])
        ->check();


        $this->assertEquals(true, $this->checker->isSatisfied());
    }

    public function testIniFail()
    {
        $this->checker->resetRequirements();

        $setting = ini_get('file_uploads');
        if ($setting == 'On' || $setting == '1') {
            $setting = true;
        } elseif ($setting == 'Off' || $setting == '' || $setting == '0') {
            $setting = false;
        }

        $this->checker
        ->requireIniValues([
            'file_uploads' => !$setting
        ])
        ->check();


        $this->assertEquals(false, $this->checker->isSatisfied());
    }

    public function phpVersionFailData()
    {
        $phpversion = (float) phpversion();

        // Higher than current
        $data[] = $phpversion + 1;
        // Lower than current
        $data[] = $phpversion - 1;

        // comparison operators
        $data[] = '>' . ($phpversion + 1);
        $data[] = '>=' . ($phpversion + 2);
        $data[] = '<' . $phpversion;
        $data[] = '>=' . $phpversion;
        $data[] = '=<' . ($phpversion - 1);

        return [$data];
    }

    public function phpVersionPassData()
    {
        $phpversion = (float) phpversion();

        $data[] = '=' . phpversion();

        // comparison operators
        $data[] = '>' . ($phpversion - 1);
        $data[] = '>=' . $phpversion;
        $data[] = '<' . ($phpversion + 1);
        $data[] = '>=' . $phpversion;
        $data[] = '=<' . ($phpversion + 1);

        return [$data];
    }
}
