<?php

class Whatever
{
    /**
     * @var string
     * @param $first string
     * @param $second bool
     *
     * @OneLine(property="value")
     * @MultiLine(
     *     properties={
     *         @OneLine(property="value")
     *     }
     * )
     *
     * Test test test:
     *   - item
     *   - item
     *
     * @return string Multiline
     *     comment test
     *     test test
     */
    public $foo;
}
