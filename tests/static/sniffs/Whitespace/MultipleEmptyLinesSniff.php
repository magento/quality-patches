<?php
/**
 * © Copyright 2013-present Adobe. All rights reserved.
 *
 * This file is licensed under OSL 3.0 or your existing commercial license or subscription
 * agreement with Magento or its Affiliates (the "Agreement).
 *
 * You may obtain a copy of the OSL 3.0 license at http://opensource.org/licenses/osl-3.0.php Open
 * Software License (OSL 3.0) or by contacting engcom@adobe.com for a copy.
 *
 * Subject to your payment of fees and compliance with the terms and conditions of the Agreement,
 * the Agreement supersedes the OSL 3.0 license with respect to this file.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Sniffs\Whitespace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Class MultipleEmptyLinesSniff
 */
class MultipleEmptyLinesSniff implements Sniff
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return [T_WHITESPACE];
    }

    /**
     * {@inheritdoc}
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if ($phpcsFile->hasCondition($stackPtr, T_FUNCTION)
            || $phpcsFile->hasCondition($stackPtr, T_CLASS)
            || $phpcsFile->hasCondition($stackPtr, T_INTERFACE)
        ) {
            if ($tokens[($stackPtr - 1)]['line'] < $tokens[$stackPtr]['line']
                && $tokens[($stackPtr - 2)]['line'] === $tokens[($stackPtr - 1)]['line']
            ) {
                // This is an empty line and the line before this one is not
                // empty, so this could be the start of a multiple empty line block
                $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr, null, true);
                $lines = $tokens[$next]['line'] - $tokens[$stackPtr]['line'];
                if ($lines > 1) {
                    $error = 'Code must not contain multiple empty lines in a row; found %s empty lines';
                    $data = [$lines];
                    $phpcsFile->addError($error, $stackPtr, 'MultipleEmptyLines', $data);
                }
            }
        }
    }
}
