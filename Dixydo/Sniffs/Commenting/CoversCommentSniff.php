<?php

declare(strict_types=1);

namespace Dixydo\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\Annotation\Annotation;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class CoversCommentSniff implements Sniff
{
    public const CODE_MISSING = 'Missing';
    public const CODE_MISSING_CLASS = 'MissingClass';
    public const CODE_DUPLICATE = 'Duplicate';

    private const MESSAGE_MISSING = 'Missing @covers annotation for test class';
    private const MESSAGE_MISSING_CLASS = 'Missing class name under test in @covers annotation';
    private const MESSAGE_DUPLICATE = 'Test class must have only one @covers annotation';

    private const ANNOTATION_NAME = '@covers';

    /**
     * @return array<int, string>
     */
    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * {@inheritDoc}
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $namePtr = TokenHelper::findNext($phpcsFile, [T_STRING], $stackPtr + 1);
        if (substr($tokens[$namePtr]['content'], -4) !== 'Test') {
            // Class is not a test.
            return;
        }

        $ptr = TokenHelper::findFirstNonWhitespaceOnPreviousLine($phpcsFile, $stackPtr);

        if ($ptr === null || $tokens[$ptr]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            $phpcsFile->addError(self::MESSAGE_MISSING, $stackPtr, self::CODE_MISSING);

            return;
        }

        $ptr = TokenHelper::findPrevious($phpcsFile, [T_DOC_COMMENT_OPEN_TAG], $ptr - 1);
        /** @var array<string, array<int, Annotation>> $annotations */
        $annotations = AnnotationHelper::getAnnotations($phpcsFile, $ptr);

        $found = false;

        foreach ($annotations as $name => $annotationGroup) {
            if ($name !== self::ANNOTATION_NAME) {
                continue;
            }

            if (count($annotationGroup) > 1) {
                $phpcsFile->addError(self::MESSAGE_DUPLICATE, $ptr, self::CODE_DUPLICATE);

                return;
            }

            $found = true;

            if (empty($annotationGroup[0]->getContent())) {
                $phpcsFile->addError(self::MESSAGE_MISSING_CLASS, $ptr, self::CODE_MISSING_CLASS);

                return;
            }
        }

        if (!$found) {
            $phpcsFile->addError(self::MESSAGE_MISSING, $ptr, self::CODE_MISSING);
        }
    }
}
