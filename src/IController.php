<?php
/**
 * Created by OxCRM.
 * User: Александр
 * Date: 01.06.2015
 * Time: 21:30
 */
namespace oxCore;
interface IController {
    public function view();
    public function post();
}


/*
 *Для реализации интерфейса:
 * public function name(IController $object){ //IController - имя реализуемого класса или интерфейса.
 * }
 */