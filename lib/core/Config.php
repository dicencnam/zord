<?php
/**
* Config
* @package Micro
* @subpackage Core
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Config {

	 /**
	 *
	 * Create code for variables
	 *
	 * @param string $name Variable name
	 * @param mixed $vars Variable content
	 * @param string $key Variable type (arraykey => array whith key,
	 * array => array, const => constant, var =>  simple variable)
	 * @return array
	 */
	private static function _compilPHP($name,$vars,$key) {
		switch($key){
				case 'arraykey':
						$str = "\n\$".$name." = ".var_export($vars, true).";\n";
				break;
				case 'array':
						$str = "\n\$".$name." = ".preg_replace("/'?\w+'?\s+=>\s+/", '', var_export($vars, true)).";\n";
				break;
				case 'const':
						$str = "\ndefine('".$name."',".var_export($vars, true).");\n";
				break;
				case 'string':
				case 'bool':
				case 'integer':
				case 'float':
						$str = "\n\$".$name." = ".var_export($vars, true).";\n";
				break;
		}
		return $str;
	}

	/**
	 *  Save variable to PHP format
	 *
	 *	$vars =array(
	 *		'var_name' => array(
	 *			'type' => 'arraykey',
	 *			'comment' => 'The comment',
	 *			'val' => $content
	 *		)
	 *	);
	 * @param string $file Path to the file where to write the data
	 * @param array $vars Variables content and type
	 * @param string $comment Comment string
	 */
	public static function saveToPHP($file,$vars, $comment){
		$php = "<?php\n";

		if($comment!='')
			$php .= "/* ".$comment." */\n";

		foreach($vars as $k => $v){
			if(isset($v['type'])){
				if(isset($v['comment']))
					$php .= "\n/* ".$v['comment'].' */';
				$php .= self::_compilPHP($k,$v['val'],$v['type']);
			}
		}
		$php .= "\n?>";
		file_put_contents($file,$php);
	}
}
?>
