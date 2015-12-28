<?php
/**
* Configer
* @package micro
* @subpackage core
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Configer {

	private function _define($source,$default){
		preg_match_all( "/define\(\s*'([^']+)'\s*\,\s*(.*?)\s*\);/si", $source, $matches);
		$result = array();
		$i = 0;
		foreach ($default as $key => $value) {
			$v = $value['value'];
			$_key = isset($value['rename']) ? $value['rename'] : $key;
			if(isset($matches[2][$i]) && $_key==$matches[1][$i]){
				switch ($value['type']) {
					case 'string':
						$v = trim($matches[2][$i],"'\"");
					break;
					case 'boolean':
						if(in_array($matches[2][$i], array('true','false','1','0','TRUE','FALSE','on','off'))){
							if(in_array($matches[2][$i], array('true','1','TRUE','on')))
								$v = true;
							else
								$v = false;
						} else {
							$v = $value['value'];
						}
					break;
					case 'integer':
						$v = filter_var($matches[2][$i], FILTER_VALIDATE_INT) ? (int) $matches[2][$i] : (int) $value['value'];
					break;
					case 'email' :
						return filter_var($matches[2][$i], FILTER_VALIDATE_EMAIL) ? $matches[2][$i] : $default;
					break;
					case 'url' :
						return filter_var($matches[2][$i], FILTER_VALIDATE_URL) ? $matches[2][$i] : $default;
					break;
				}
			}
			$result[$key] = $value;
			$result[$key]['value'] = $v;
			$i++;
		}
		return $result;
	}

	private function _orm($source,$default,$name){
		$prefix = strtoupper($name).'_';
		preg_match("#ORM::configure\(\s*'(.*?):host=(.*?);dbname=(.*?)'\s*,\s*null\s*,\s*'(.*?)'\s*\)#", $source, $mCONNEXION);
		preg_match("#ORM::configure\(\s*'username'\s*,\s*'(.*?)',\s*'(.*?)'\s*\)#", $source, $mUSERNAME);
		preg_match("#ORM::configure\(\s*'password'\s*,\s*'(.*?)',\s*'(.*?)'\s*\)#", $source, $mPASSWORD);

		$db = array();
		$db['ENGINE'] = (isset($mCONNEXION[1])) ? $mCONNEXION[1] : $default[$prefix.'ENGINE']['value'];
		$db['HOST'] = (isset($mCONNEXION[2])) ? $mCONNEXION[2] : $default[$prefix.'ENGINE']['value'];
		$db['DBNAME'] = (isset($mCONNEXION[3])) ? $mCONNEXION[3] : $default[$prefix.'DBNAME']['value'];
		$db['ID'] = (isset($mCONNEXION[4])) ? $mCONNEXION[4] : $default[$prefix.'ID']['value'];
		$db['USERNAME'] = (isset($mUSERNAME[1])) ? $mUSERNAME[1] : $default[$prefix.'USERNAME']['value'];
		$db['PASSWORD'] = (isset($mPASSWORD[1])) ? $mPASSWORD[1] : $default[$prefix.'PASSWORD']['value'];
		$result = array();

		$labels = array('ENGINE','HOST','DBNAME','ID','USERNAME','PASSWORD');
		foreach ($labels as $n) {
			$type = 'string';
			if($n=='ID')
				$type = 'hidden';
			$result[$prefix.$n] = array(
				'comment' => $default[$prefix.$n]['comment'],
				'type' => $type,
				'value' => $db[$n],
			);
		}
		return $result;
	}

	public function load($name,$default=array(),$type='define'){
		$file = CONFIG_FOLDER.'config_'.$name.'.php';
		if(file_exists($file)){
			$source = file_get_contents($file);
			switch ($type) {
				case 'orm':
					$result = $this->_orm($source,$default,$name);
				break;
				default: //define
					$result = $this->_define($source,$default);
				break;
			}
		}	else {
			$result = $default;
		}
		return $result;
	}

	public function toHTML($conf,$name){
		$html = '<div class="div_config"><h2>'.strtoupper($name).'</h2>'.PHP_EOL;
		foreach ($conf as $key => $value) {
			if($value['type']!='hidden' && $value['type']!='hidden_boolean' && $value['type']!='hidden_integer'){
				$html .= '<h3>'.$key.'</h3>'.PHP_EOL;
				$html .= '<p>'.$value['comment'].'</p>'.PHP_EOL;
				switch ($value['type']) {
					case 'string':
						$html .= ' <input name="'.$key.'" type="text" value="'.$value['value'].'"/>'.PHP_EOL;
					break;
					case 'email' :
						$html .= ' <input name="'.$key.'" type="email" value="'.$value['value'].'"/>'.PHP_EOL;
					break;
					case 'url' :
						$html .= ' <input name="'.$key.'" type="url" value="'.$value['value'].'"/>'.PHP_EOL;
					break;
					case 'integer':
						$html .= ' <input name="'.$key.'" type="number" value="'.$value['value'].'"/>'.PHP_EOL;
					break;
					case 'boolean':
						$Y = '';
						$N = '';
						if($value['value'])
							$Y = ' checked="checked"';
						else
							$N = ' checked="checked"';
						$html .= '<label>Yes <input type="radio" name="'.$key.'" value="true" '.$Y.'/></label> <label>No <input type="radio" name="'.$key.'" value="false" '.$N.'/></label>'.PHP_EOL;
					break;
				}
				$html .= '<br/><br/>'.PHP_EOL;
			} else {
				$html .= ' <input name="'.$key.'" type="hidden" value="'.$value['value'].'"/>'.PHP_EOL;
			}
		}
		$html .= '</div>';
		return $html;
	}

	public function toHTMLForm($configs,$action,$addHTML=''){
		$html = '<form method="POST" action="'.$action.'">'.PHP_EOL;
		foreach ($configs as $value)
			$html .= $value;
		$html .= $addHTML.'<p style="text-align:center;"><input type="submit" name="configer"/></p><br/><br/></form>'.PHP_EOL;
		return $html;
	}

	public function loadConfigs($configs,$action,$addHTML=''){
		$result = array();
		$html = array();

		foreach ($configs as $name => $default) {
			$v = $this->load($name,$default['definition'],$default['type']);
			$h = $this->toHTML($v,$name);
			$result[$name] = array('value'=>$v,'html'=>$h);
			$html[] = $h;
		}
		$result['ALL_CONFIGS_HTML'] = $this->toHTMLForm($html,$action,$addHTML);
		return $result;
	}

	public function saveConfigs($configs,$post){
		foreach ($configs as $key => $value) {
			$file = CONFIG_FOLDER.'config_'.$key.'.php';
			$php = "<?php\n";

			$php .= "/* ".$key." */\n";

			$vars = array();
			switch ($value['type']) {
				case 'orm':
					$prefix = strtoupper($key).'_';
					$php .= "ORM::configure('".trim($post[$prefix.'ENGINE']).":host=".trim($post[$prefix.'HOST']).";dbname=".trim($post[$prefix.'DBNAME'])."', null, '".trim($post[$prefix.'ID'])."');\n";
					$php .= "ORM::configure('username', '".trim($post[$prefix.'USERNAME'])."', '".trim($post[$prefix.'ID'])."');\n";
					$php .= "ORM::configure('password', '".trim($post[$prefix.'PASSWORD'])."', '".trim($post[$prefix.'ID'])."');\n";
				break;
				default: //define
					foreach ($value['definition'] as $k => $v) {
						$n = $k;
						if(isset($v['rename']))
							$n = $v['rename'];
						$php .= "\n/* ".$v['comment'].' */';
						switch ($v['type']) {
							case 'integer':
							case 'boolean':
							case 'hidden_integer':
							case 'hidden_boolean':
								$php .= "\ndefine('".$n."',".trim($post[$k]).");\n";
							break;
							default:
								$php .= "\ndefine('".$n."',".var_export(trim($post[$k]), true).");\n";
							break;
						}

					}
				break;
			}

			$php .= "\n?>";
			file_put_contents($file, $php);
		}
	}
}
?>
