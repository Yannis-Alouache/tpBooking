<?php

abstract class Template {
    abstract public function render($context) : string;
}