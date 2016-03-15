<?php
/**
 * Created by OxGroup.
 * User: Александр
 * Date: 01.06.2015
 * Time: 21:30
 */
namespace Ox;

/**
 * Interface IController
 *
 * @package Ox
 */
interface IController
{
    public function get();
    
    public function post();
}

/*
 *Для реализации интерфейса:
 * public function name(IController $object){ //IController - имя реализуемого класса или интерфейса.
 * }
 */
