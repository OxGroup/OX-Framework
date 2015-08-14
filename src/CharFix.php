<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 19.05.15
 * Time: 14:38
 */

/**
 * Class CharFix
 */
namespace Ox;
class CharFix
{

    public $text;
    public $error;

    /**
     * @param string $text
     * @return mixed|string
     */
    public function char($text = "")
    {
        $text = preg_replace("/[^a-z]/i", "", $text);
        return $text;
    }

    public function number($text = "")
    {
        $text = preg_replace("/[^0-9]/i", "", $text);
        return $text;
    }

    public function charNumber($text = "")
    {
        $text = preg_replace("/[^a-z0-9]/i", "", $text);
        return $text;
    }

    public function noHtml($text = "")
    {
        $text = htmlspecialchars($text, ENT_QUOTES);
        return $text;
    }

    public function noSpec($text = "")
    {
        $text = preg_replace("#[" . preg_quote("'`\\|>!`~^<%)(&") . "]#", "", $text);
        $text = str_replace('"', '\"', $text);
        return $text;
    }



    public function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    public function str2url($str) {
        // переводим в транслит
        $str = $this->rus2translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        return $str;
    }
    /**
     * @param string $text
     * @param string $cfg
     * @return string
     */
    public function FixAll($text = "", $cfg = "")
    {
        $this->text = $text;
        $cfg = explode("|", $cfg);
        foreach ($cfg as $val) {
            switch ($val) {
                case("noSpec"):
                    $this->text = $this->noSpec($this->text);
                    break;

                case("noHtml"):
                    $this->text = $this->noHtml($this->text);
                    break;

                case("charNumber"):
                    $this->text = $this->charNumber($this->text);
                    break;

                case("char"):
                    $this->text = $this->char($this->text);
                    break;

                case("number"):
                    $this->text = $this->number($this->text);
                    break;
            }
        }
        return $this->text;
    }

    /**
     * @param $text
     * @param $cfg
     * @return bool
     */
    public function valid($text, $cfg, $name = "", $jsname = "")
    {

        $this->text = $text;
        $cfg = explode("|", $cfg);
        foreach ($cfg as $val) {
            switch ($val) {
                case("noSpec"):
                    $this->text = $this->noSpec($this->text);
                    break;

                case("noHtml"):
                    $this->text = $this->noHtml($this->text);
                    break;

                case("charNumber"):
                    $this->text = $this->charNumber($this->text);
                    break;

                case("char"):
                    $this->text = $this->char($this->text);
                    break;

                case("number"):
                    $this->text = $this->number($this->text);
                    break;

                case("pass"):

                    $text = explode("|", $this->text);
                    if ($text['0'] != $text['1']) {
                        $this->error[$name]['pass'] = "Пароли не совпадают";
                        return false;
                    } else {
                        $this->text = $text['0'];
                    }
                    break;

                case("mail"):

                    if (!preg_match("/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,4}$/i", $this->text)) {
                        $this->error[$name]['mail'] = "Неверный формат";
                        return false;
                    } else {
                        return true;
                    }
                    break;

                case("domain"):
                    if (!preg_match('/^((www\.)?([A-Za-z0-9\-]+\.)+([A-Za-z]+){2,4})(\:(\d)+)?(\/(.*))?$/i', $this->text)) {
                        $this->error[$name]['domain'] = "Неверный формат";
                        return false;
                    } else {
                        return true;
                    }
                    break;

                case(preg_match('/min:*/', $val) ? true : false):

                    $valcou = explode(":", $val);
                    $count = iconv_strlen($this->text, 'UTF-8');


                    if ($count < $valcou['1']) {
                        $this->error[$name]['min'] = "Требуется минимум {$valcou['1']} символов";
                        return false;
                    } else {
                        return true;
                    }
                    break;

                case(preg_match('/max:*/', $val) ? true : false):
                    $valcou = explode(":", $val);
                    $count = iconv_strlen($this->text, 'UTF-8');

                    if ($count > $valcou['1']) {
                        $this->error[$name]['max'] = "Максимум символов {$valcou['1']}";
                        return false;
                    } else {
                        return true;
                    }
                    break;


            }
        }
        if ($this->text == $text) {
            return true;
        } else {
            $this->error['char'][$name] = "Недопустимые символы";
            return false;
        }
    }


    public function viewErrors()
    {
        if (!empty($this->error) and $this->error != "") {
            foreach ($this->error as $key => $val) {
                $error[$key] = implode(",", $val);
            }
            return $error;
        } else {
            return false;
        }

    }

//$new = htmlspecialchars($insql, ENT_QUOTES);

}