<?php
/**
* TEI header parser
* @package zord
* @subpackage metadata
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Tei {

	/**
	* Check user
	*
	* @param String $header XML TEI header string
	* @param String $source Portal URL
	* @return array
	*/
	static public function parseHeader($header,$source){

		$header = preg_replace('#xml:id#us','id',$header);
		$header = preg_replace('#xml:lang#us','xmllang',$header);
		$data = simplexml_load_string($header);

		$l = explode('-',LANG);
		$metadata = array(
			'generator' => ZORD.' '.ZORD_VERSION,
			'source' => $source,
			'language' => $l[0]
		);

		// Title
		if(isset($data->filedesc->titlestmt->title)){
			foreach ($data->filedesc->titlestmt->title as $title){
				if(isset($title['type']) && $title['type']=='sub')
					$metadata['subtitle'] = $title.'';
				else
					$metadata['title'] = $title.'';
			}
		}

		// creator
		$metadata['creator'] = array();

		if(isset($data->filedesc->titlestmt->author)){
			foreach ($data->filedesc->titlestmt->author as $author)
				$metadata['creator'][] = trim($author['key'].'');
		}

		// Contributor
		// editor
		if(isset($data->filedesc->titlestmt->editor)){
			if(!isset($metadata['editor']))
				$metadata['editor']  = array();

			foreach ($data->filedesc->titlestmt->editor as $editor)
				$metadata['editor'][] = trim($editor['key'].'');
		}

		// category
		if(isset($data->filedesc->seriesstmt)){
			if(isset($data->filedesc->seriesstmt['id']))
				$metadata['category'] = array(trim($data->filedesc->seriesstmt['id'].''));
			if(isset($data->filedesc->seriesstmt['n']))
				$metadata['category_number'] = trim($data->filedesc->seriesstmt['n'].'');
		}

		if(isset($data->filedesc->extent->measure)){
			foreach ($data->filedesc->extent->measure as $measure){
				$unit = $measure['unit'].'';
				if($unit=='pages'){
					$metadata['pages']  = $measure['quantity'].'';
				}
			}
		}

		// Publisher unqualified
		if(isset($data->filedesc->sourcedesc->biblfull->publicationstmt->publisher))
			$metadata['publisher'] = $data->filedesc->sourcedesc->biblfull->publicationstmt->publisher.'';

		// pubPlace
		if(isset($data->filedesc->sourcedesc->biblfull->publicationstmt->pubplace)){
			$pubplace = $data->filedesc->sourcedesc->biblfull->publicationstmt->pubplace.'';
			if($pubplace!='')
				$metadata['pubplace'] = $pubplace;
		}

		// Type
		$metadata['type'] = 'Book';
		// Format
		$metadata['format'] = 'text/html';

		// Coverage
		// ?

		// Rights
		if(isset($data->filedesc->sourcedesc->biblfull->publicationstmt->publisher))
			$metadata['rights'] = '© '.$data->filedesc->sourcedesc->biblfull->publicationstmt->publisher;

		// Audience
		// ?

		// relation
		if(isset($data->filedesc->sourcedesc->biblfull->seriesstmt->title)){
			$relation = array();
			foreach($data->filedesc->sourcedesc->biblfull->seriesstmt->title as $line){
				$type = '';
				if(isset($line['type']))
					$type = $line['type'].'';

				if($type=='num')
					$metadata['collection_number'] = $line.'';
				else if($type=='')
					$relation[] = $line.'';
			}
			$metadata['relation'] = implode(", ", $relation);
		}

		// date
		if(isset($data->filedesc->sourcedesc->biblfull->publicationstmt->date)){
			$metadata['date'] = Tei::setDateInteger($data->filedesc->sourcedesc->biblfull->publicationstmt->date['when'].'');
		}

		// Identifier
		if(isset($data->filedesc->sourcedesc->biblfull->publicationstmt->idno)){
			foreach ($data->filedesc->sourcedesc->biblfull->publicationstmt->idno as $idno) {
				$type = $idno['type'].'';
				if($type=='ISBN-13'){
					$metadata['identifier_isbn'] = $idno.'';
					$metadata['identifier_uri'] = $metadata['source'];
				}
			}
		}
		if(isset($data->filedesc->notesstmt->note)){
			foreach ($data->filedesc->notesstmt->note as $note) {
				$type = $note['type'].'';
				if($type=="ref"){
					$metadata['ref_n'] = $note->ref['n'].'';
					$metadata['ref_url'] = $note->ref['target'].'';
				}

				if($type=="image"){
					$metadata['ref_cover'] = $note->graphic['url'].'';
				}
			}
		}

		if(isset($data->profiledesc->creation->date)){
			if(isset($data->profiledesc->creation->date['when'])){
				if($data->profiledesc->creation->date['when']!='')
					$metadata['creation_date'] = Tei::setDateInteger($data->profiledesc->creation->date['when'].'');
			} else if(isset($data->profiledesc->creation->date['notbefore'])){
				if($data->profiledesc->creation->date['notbefore']!='')
					$metadata['creation_date'] = Tei::setDateInteger($data->profiledesc->creation->date['notbefore'].'');
			} else if(isset($data->profiledesc->creation->date['notafter'])){
				if($data->profiledesc->creation->date['notafter']!='')
					$metadata['creation_date_after'] = Tei::setDateInteger($data->profiledesc->creation->date['notafter'].'');
			} else if(isset($data->profiledesc->creation->date['from'])){
				if($data->profiledesc->creation->date['from']!='')
					$metadata['creation_date'] = Tei::setDateInteger($data->profiledesc->creation->date['from'].'');
			} else if(isset($data->profiledesc->creation->date['to'])){
				if($data->profiledesc->creation->date['to']!='')
					$metadata['creation_date_after'] = Tei::setDateInteger($data->profiledesc->creation->date['to'].'');
			}
		}

		if(isset($data->profiledesc->langusage->language))
			$metadata['language'] = $data->profiledesc->langusage->language['ident'].'';

		$category = array();
		if(isset($data->profiledesc->textclass)){
			if(isset($data->profiledesc->textclass->keywords)){
				foreach ($data->profiledesc->textclass->keywords as $keywords){
					$scheme = $keywords['scheme'].'';
					$load = false;
					if($scheme=='BISAC' || $scheme=='CLIL')
						$load = true;
					if($scheme=='Droz' && $keywords['xmllang']=='fr')
						$load = true;
					if($load){
						foreach ($keywords->term as $term){
							$category[] = mb_strtoupper(trim($term.''));
						}
					}
				}
			}
		}
		if(count($category)>0)
			$metadata['category']  = $category;

		return $metadata;
	}

	/**
	* Set year date
	*
	* @param string $date
	* @return integer Year
	*/
	static protected function setDateInteger($date){
		$date = explode('-',$date);
		return (int) $date[0];
	}
}
?>
