<?php

namespace Dixydo\Sniffs\Commenting;

use SlevomatCodingStandard\Sniffs\TestCase;

/**
 * @covers CoversCommentSniff
 */
class CoversCommentSniffTest extends TestCase
{
    public function testErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/data/coversCommentErrors.php');
        self::assertSniffError($report, 3, CoversCommentSniff::CODE_MISSING);
        self::assertSniffError($report, 7, CoversCommentSniff::CODE_MISSING_CLASS);
        self::assertSniffError($report, 14, CoversCommentSniff::CODE_DUPLICATE);
    }

    public function testNoErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/data/coversCommentNoErrors.php');
        self::assertNoSniffErrorInFile($report);
    }
}
