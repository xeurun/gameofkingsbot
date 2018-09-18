<?php

namespace Screens;

abstract class BaseScreen
{
    protected $chatId;
    protected $title;

    public function __construct($chatId, $title)
    {
        $this->chatId = $chatId;
        $this->title = $title;
    }
}
