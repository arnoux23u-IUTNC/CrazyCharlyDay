<?php

namespace custombox\mvc;

abstract class Renderer
{

    const SHOW = 0;
    const SHOW_IN_LIST = 1;
    const SHOW_ALL = 2;
    const CREATE = 3;
    const HOME_HOME = 5;
    const LOGIN = 20;
    const REGISTER = 21;



    const OTHER_MODE = 10;
    const OWNER_MODE = 100;
    const ADMIN_MODE = 1000;
}