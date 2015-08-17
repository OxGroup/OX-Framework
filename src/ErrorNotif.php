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
public static $file = __DIR__ . "/../../../../http/errors.php";
	public static function build()
	{
		if (file_exists(self::$file)) {
			include_once(self::$file);
			$errorNotif = array();
			if (!empty($_SESSION['errorNotif'])) {

				$CharFix = new CharFix();
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
						$errorNotif[] = array("title" => $CharFix->FixAll($title, "noSpec|noHtml"), "text" => $CharFix->FixAll($text, "noSpec|noHtml"), "color" => $arrayColor[$code[0]]);
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