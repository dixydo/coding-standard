<?php

class Whatever
{
    /**
     * @var  string
     * @param $first  string
     * @param $second bool
     *
     * @OneLine( property  ="value")
     * @MultiLine(
     *     properties={
     *        @OneLine(property="value")
     *    },
     *    foo="bar"
     * )
     */
    public $foo;
}
