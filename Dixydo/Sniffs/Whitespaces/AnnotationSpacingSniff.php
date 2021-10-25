<?php

declare(strict_types=1);

namespace Dixydo\Sniffs\Whitespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\Annotation\Annotation;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class AnnotationSpacingSniff implements Sniff
{
    public const CODE_DUPLICATE_SPACES = 'DuplicateSpaces';
    public const CODE_INVALID_INDENTATION = 'InvalidIndentation';

    private const INDENTATION = 4;

    /**
     * @return array<int, string>
     */
    public function register(): array
    {
        return [
            T_DOC_COMMENT_OPEN_TAG,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $annotations = AnnotationHelper::getAnnotations($phpcsFile, $stackPtr);

        foreach ($annotations as $annotationGroup) {
            foreach ($annotationGroup as $annotation) {
                if (!$annotation instanceof GenericAnnotation) {
                    $this->processNonGeneric($phpcsFile, $annotation);

                    continue;
                }

                $this->processGeneric($phpcsFile, $annotation);
            }
        }
    }

    private function processNonGeneric(File $file, Annotation $annotation): void
    {
        $tokens = $file->getTokens();

        for ($i = $annotation->getStartPointer(); $i <= $annotation->getEndPointer(); $i++) {
            if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE && $tokens[$i - 1]['code'] === T_DOC_COMMENT_STAR) {
                continue;
            }

            $this->processToken($file, $i);
        }
    }

    private function processGeneric(File $file, GenericAnnotation $annotation): void
    {
        $tokens = $file->getTokens();
        $level = 0;

        for ($i = $annotation->getStartPointer(); $i <= $annotation->getEndPointer(); $i++) {
            $content = $tokens[$i]['content'];

            if ($tokens[$i]['column'] === 1 || $tokens[$i]['code'] === T_DOC_COMMENT_STAR) {
                continue;
            }

            if ($content === $file->eolChar) {
                // Check if last char in this line is an opening bracket.
                if (in_array(substr($tokens[$i - 1]['content'], -1), ['(', '{'], true)) {
                    $level++;
                }
            }

            if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE && $tokens[$i - 1]['code'] === T_DOC_COMMENT_STAR) {
                $next = TokenHelper::findNextExcluding($file, T_DOC_COMMENT_WHITESPACE, $i);

                if (in_array(substr($tokens[$next]['content'], 0, 1), [')', '}'], true)) {
                    $level--;
                }

                $expectedIndentation = $level * AnnotationSpacingSniff::INDENTATION + 1;
                $actualIndentation = strlen($tokens[$i]['content']);

                if ($actualIndentation !== $expectedIndentation) {
                    $fixable = $file->addFixableError(
                        sprintf(
                            'Annotation has invalid indentation, expected %d, actual %d.',
                            $expectedIndentation - 1,
                            $actualIndentation - 1,
                        ),
                        $i,
                        AnnotationSpacingSniff::CODE_INVALID_INDENTATION,
                    );

                    if (!$fixable) {
                        continue;
                    }

                    $file->fixer->beginChangeset();
                    $file->fixer->replaceToken($i, str_repeat(' ', $expectedIndentation));
                    $file->fixer->endChangeset();
                }

                if ($level === 0 && substr($tokens[$next]['content'], 0, 1) === ')') {
                    // The end of annotation - simply finish processing.
                    return;
                }

                continue;
            }

            $this->processToken($file, $i);
        }
    }

    private function processToken(File $file, int $ptr): void
    {
        $tokens = $file->getTokens();
        $content = $tokens[$ptr]['content'];

        if (
            $tokens[$ptr]['column'] === 1
            || $tokens[$ptr]['code'] === T_DOC_COMMENT_STAR
            || $content === $file->eolChar
        ) {
            return;
        }

        $matchResult = preg_match_all('/\s{2,}/', $content);

        if (!$matchResult) {
            return;
        }

        $fixable = $file->addFixableError(
            'Duplicate spaces in annotation.',
            $ptr,
            AnnotationSpacingSniff::CODE_DUPLICATE_SPACES,
        );

        if (!$fixable) {
            return;
        }

        $file->fixer->beginChangeset();
        $file->fixer->replaceToken($ptr, preg_replace('~ {2,}~', ' ', $content));
        $file->fixer->endChangeset();
    }
}
