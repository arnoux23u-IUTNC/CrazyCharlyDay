<?php

namespace custombox\mvc;

abstract class Renderer
{

    const SHOW = 0;
    const SHOW_ALL = 2;
    const HOME_HOME = 5;

    const OTHER_MODE = 10;
    const OWNER_MODE = 100;
    const ADMIN_MODE = 1000;
}