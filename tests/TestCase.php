<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests;

use Closure;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\SoapFactory;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use RuntimeException;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createSettingsFromEnvironment(SoapFactory $soapFactory = null): FinkokSettings
    {
        $settings = new FinkokSettings(
            strval(getenv('FINKOK_USERNAME')),
            strval(getenv('FINKOK_PASSWORD')),
            FinkokEnvironment::makeDevelopment()
        );
        if (null !== $soapFactory) {
            $settings->changeSoapFactory($soapFactory);
        }

        /*
        $settings->soapFactory()->setLogger(
            $this->createLoggerPrinter(
                sprintf(
                    '%s/../build/tests/%s-%s-%s.txt',
                    __DIR__,
                    (new \DateTimeImmutable())->format('YmdHis.u'),
                    $this->getName(),
                    uniqid()
                )
            )
        );
        */
        return $settings;
    }

    protected function createLoggerPrinter($outputFile = 'php://stdout'): LoggerInterface
    {
        return new class($outputFile) extends AbstractLogger implements LoggerInterface {
            public $outputFile;

            public function __construct(string $outputFile)
            {
                $this->outputFile = $outputFile;
            }

            public function log($level, $message, array $context = []): void
            {
                file_put_contents(
                    $this->outputFile,
                    PHP_EOL . print_r(json_decode($message), true),
                    FILE_APPEND
                );
            }
        };
    }

    protected function waitUntil(
        Closure $checkFunction,
        int $maxSeconds,
        int $waitSeconds,
        string $exceptionMessage = ''
    ): void {
        $repeatUntil = time() + $maxSeconds;
        do {
            if ($checkFunction()) {
                return;
            }
            if (time() > $repeatUntil) {
                break;
            }
            sleep($waitSeconds);
        } while (true);
        if ('' !== $exceptionMessage) {
            throw new RuntimeException($exceptionMessage);
        }
    }

    public static function filePath(string $append = ''): string
    {
        return __DIR__ . '/_files/' . $append;
    }

    public static function fileContentPath(string $append): string
    {
        return static::fileContent(static::filePath($append));
    }

    public static function fileContent(string $path): string
    {
        if (! file_exists($path)) {
            return '';
        }
        return strval(file_get_contents($path));
    }
}
