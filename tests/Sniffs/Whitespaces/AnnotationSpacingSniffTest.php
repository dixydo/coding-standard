<?php

namespace Dixydo\Sniffs\Whitespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

/**
 * @covers AnnotationSpacingSniff
 */
class AnnotationSpacingSniffTest extends TestCase
{
    public function testErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/data/annotationSpacesErrors.php');
        self::assertSniffError($report, 6, AnnotationSpacingSniff::CODE_DUPLICATE_SPACES);
        self::assertSniffError($report, 7, AnnotationSpacingSniff::CODE_DUPLICATE_SPACES);
        self::assertSniffError($report, 10, AnnotationSpacingSniff::CODE_DUPLICATE_SPACES);
        self::assertSniffError($report, 13, AnnotationSpacingSniff::CODE_INVALID_INDENTATION, 'Annotation has invalid indentation, expected 8, actual 7.');
        self::assertSniffError($report, 14, AnnotationSpacingSniff::CODE_INVALID_INDENTATION, 'Annotation has invalid indentation, expected 4, actual 3.');
        self::assertSniffError($report, 15, AnnotationSpacingSniff::CODE_INVALID_INDENTATION, 'Annotation has invalid indentation, expected 4, actual 3.');
        self::assertNoSniffError($report, 16);
    }

    public function testNoErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/data/annotationSpacesNoErrors.php');
        self::assertNoSniffErrorInFile($report);
    }
}
