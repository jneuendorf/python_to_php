<?php

function __print(...$args) {
    echo implode(' ', $args);
}
