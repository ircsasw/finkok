<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\GetContractsResult;
use PhpCfdi\Finkok\Tests\TestCase;

class GetContractsResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('manifest-getcontracts-response.json'));
        $result = new GetContractsResult($data);
        $this->assertTrue($result->success());
        $this->assertSame('predefined-privacy', $result->privacy());
        $this->assertSame('predefined-contract', $result->contract());
        $this->assertSame('predefined-error', $result->error());
    }
}
