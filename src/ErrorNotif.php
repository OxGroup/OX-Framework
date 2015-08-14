<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 22.06.15
 * Time: 15:11
 */

namespace Ox;


/**
 * Class ErrorNotif
 * @package OxApp\models
 */
class ErrorNotif extends AbstractModel
{

	public static function build($file = __DIR__ . "/../../../../http/errors.php")
	{

		if (file_exists($file)) {
			include_once(__DIR__ . "/../../../../http/errors.php");
			$errorNotif = array();
			if (!empty($_SESSION['errorNotif'])) {

				$charfix = new charFix();
				foreach ($_SESSION['errorNotif'] as $val) {

					$code = @explode(".", $val['code']);
					if (!empty($code[0])) {
						$title = key($arrayErrors[$code[0]]);
						$text = $arrayErrors[$code[0]][$title][$code[1]];

						if (!empty($val['array'])) {
							foreach ($val['array'] as $titleCode => $valCode) {
								$text = str_replace(":{$titleCode}", $valCode, $text);
							}
						}
						$errorNotif[] = array("title" => $charfix->FixAll($title, "noSpec|noHtml"), "text" => $charfix->FixAll($text, "noSpec|noHtml"), "color" => $arrayColor[$code[0]]);
					}
				}
				$_SESSION['errorNotif'] = array();
			}

			return $errorNotif;
		} else {
			return false;
		}
	}

	public static function AddAlert($code, $array = "")
	{
		if (empty($_SESSION['errorNotif']))
			$_SESSION['errorNotif'] = array();
		$_SESSION['errorNotif'][] = array("code" => $code, "array" => $array);
	}
}