<?php
foreach (glob(__DIR__.'/*.php') as $interface) {
    include_once $interface;
}
