<?php
/**
 * Created by OxCRM.
 * User: Александр
 * Date: 24.05.2015
 * Time: 23:04
 */

/**
 * Class tableBuilder
 */
namespace oxCore;
class tableBuilder
{

    public $thoption;
    public $tboption;
    private $table;
    private $th;
    private $tb;
    private $tf;


    /**
     * @param $array
     */
    public function setTable($array)
    {
        if ($array != '') {
            foreach ($array as $key => $val) {
                $this->table .= " {$key}=\"{$val}\"";
            }
        }
    }

    public function setHead($array)
    {

        if ($array != '') {
            $this->th .= "<tr>";
            foreach ($array as $val) {
                $option = "";
                $keyChar = explode("|", $val);
                if (isset($keyChar['1'])) {
                    $option = " {$keyChar['1']}";
                    $val = "{$keyChar['0']}";
                }
                $this->th .= "<th{$option}>{$val}</th>\n";
            }
            $this->th .= "</tr>";
        }
    }

    public function SetFooter()
    {

    }

    public function setBody($array)
    {
        if ($array != '') {
            foreach ($array as $array2) {

                $this->tb .= "<tr>";
                foreach ($array2 as $val) {
                    $option = "";
                    $keyChar = explode("|", $val);
                    if (isset($keyChar['1'])) {
                        $option = " {$keyChar['1']}";
                        $val = "{$keyChar['0']}";
                    }
                    $this->tb .= "<td{$option}>{$val}</td>\n";
                }
                $this->tb .= "</tr>";
            }
        }

    }

    public function build()
    {

        if ($this->th != "") {
            if ($this->thoption != "") $this->thoption = " " . $this->thoption;
            $this->th = "<thead{$this->thoption}>\n" . $this->th . "</thead>";
            $this->tb = "<tbody{$this->tboption}>\n" . $this->tb . "</tbody>";
        }
        $build = "<table{$this->table}>
            {$this->th}
            {$this->tb}
        </table>";
        return $build;

    }

    public function Clean()
    {
        foreach ($this as $k => $v) {
            $this->{$k} = "";
        }
    }

}