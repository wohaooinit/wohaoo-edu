<?php
App::uses('MobileAppController', 'Mobile.Controller');
App::uses('HttpSocket', 'Network/Http');
App::uses('Inflector', 'Utility');
App::uses('CakeNumber', 'Utility');

require_once APP. DS. 'Lib'. DS . 'twitteroauth/twitteroauth.php';

/**
 * MobileProsController
 *
 */
class MobileProsController extends MobileAppController {
	public $uses = false; //no model, no table
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('index', 'view', 'comments', 'stats', 'info', 'news');
	}
	
	/**
	  //build the following array
		array(
		    	"identifier" => "id",
		        "idAttribute" => "id",
			"label" => "name",
			"items" => array( {
						model => 'pro',
						id,
						icon,
						name,
						value,
						ranking,
						club
					 }
			)
	 */
	public function index($q = "", $l = ""){
		$this->log("<index q=$q l=$l>", 'debug');
		$this->viewClass = "Json";
		try{
			if(isset($this->request->query['q']))
				$q = $this->request->query['q'];
			if(isset($this->request->query['l']))
				$l = $this->request->query['l'];
			$this->log("q=$q, l=$l", 'debug');
			$pros = $this->requestAction("/pros/index?q=$q&l=$l");
			//$this->log("pros=" . var_export($pros, true), 'debug');
			$items = array();
			
			foreach($pros as $pro){
				$item = array();
				$item["model"] = "pro";
				$item["id"] = $pro['Pro']['id'];
				$item["name"] = $pro["ProPerson"]["per_first_name"] . " " . $pro["ProPerson"]["per_last_name"];
				$item["icon"] = "/images/image?id=" .  $pro["ProPerson"]["per_img_id"] . "&width=32&height=32";
				$item["value"] =  "<strong>" . __("Value") . ":</strong>"  . "USD$" . $pro['Pro']['pro_value'];
				$item["ranking"] = "<strong>" . __("Ranking") . ":</strong>"  . '#' .   $pro['Pro']['pro_ranking'];
				$item["club"] = $pro["ProClub"]["clb_name"];
				$item["uniqueid"] = $pro['Pro']['pro_ranking'];
				$items[] = $item;
			}
			
			$identifier = "id";
			$idAttribute = "id";
			$label = 'name';
			$data = compact("identifier", "idAttribute", "label", "items");
			$this->log("</index q=$q>", 'debug');
			if(!empty($this->request->params['requested'])){
				return $data;
			}
			$this->set($data);
			$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
		}catch(Exception $e){
			$this->log($e->getMessage());
		}
	}
	/**
	 //build the following array
		array(
		    	"identifier" => "id",
		        "idAttribute" => "id",
			"label" => "name",
			"items" => array( {
						model => 'pro',
						id,
						icon,
						name,
						value,
						ranking,
						club
					 }
			)
	 */
	public function view($id = null){
		$this->viewClass = "Json";
		try{
			$pro = $this->requestAction("/pros/view/$id");
			$items = array();
			$item = array();
			$item["model"] = "pro";
			$item["id"] = $id;
			$item["name"] = $pro["ProPerson"]["per_first_name"] . " " . $pro["ProPerson"]["per_last_name"];
			$item["short_name"] = $this->truncate($item['name'], 15);
			$item["icon"] = "/images/image?id=" .  $pro["ProPerson"]["per_img_id"] . "&width=32&height=32";
			$item["value"] =  "<strong>" . __("Value") . "</strong>"  . "USD$" . $pro['Pro']['pro_value'];
			$item["ranking"] = "<strong>" . __("Ranking") . "</strong>" . '#' .  $pro['Pro']['pro_ranking'];
			$item["club"] = $pro["ProClub"]["clb_name"];
			$items[] = $item;
			
			$identifier = "id";
			$idAttribute = "id";
			$label = 'name';
			$data = compact("identifier", "idAttribute", "label", "items");
			if(!empty($this->request->params['requested'])){
				return $data;
			}
			$this->set($data);
			$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
		}catch(Exception $e){
			$this->log($e->getMessage());
		}
	}
	
	private function truncate($text = "", $length = 0){
		if($length <= 0 || strlen($text) <= $length)
			return $text;
		return substr($text, 0, $length);
	}
	
	/**
	 //build the following array
		array(
		    	"identifier" => "id",
		        "idAttribute" => "id",
			"label" => "name",
			"items" => array( {
						model => 'pro',
						id,
						icon,
						name,
						value,
						ranking,
						club,
						children => [
							{
								model => 'attribute',
								id,
								name,
								value
							 }
						]
					 }
			)
	 */
	public function info($id = null){
		$this->log("<info id=$id>", 'debug');
		$this->viewClass = "Json";
		try{
			$pro = $this->requestAction("/pros/view/$id");
			$items = array();
			$item = array();
			$item["model"] = "pro";
			$item["id"] = $id;
			$item["name"] = $pro["ProPerson"]["per_first_name"] . " " . $pro["ProPerson"]["per_last_name"];
			$item["short_name"] = $this->truncate($item['name'], 15);
			$item["icon"] = "/images/image?id=" .  $pro["ProPerson"]["per_img_id"] . "&width=32&height=32";
			$item["value"] = "USD$0";
			$item["ranking"] = "#". $pro['Pro']['pro_ranking'];
			$item["club"] = $pro["ProClub"]["clb_name"];
			$children = array();
			
			$attributes = $this->requestAction("/attributes/table/Pro/$id");
			$this->log("attributes=" . var_export($attributes, true), 'debug');
			foreach($attributes as $attribute){
				$child = array();
				$child['model'] = 'attribute';
				$child['id'] = 'att-' . $attribute['Attribute']['id'];
				$child['name'] = __(trim($attribute['Definition']['atd_display_name']));
				$value = '';
				$is_image = $attribute['Definition']['atd_is_image'];
				$is_data = $attribute['Definition']['atd_is_data'];
				$is_date = $attribute['Definition']['atd_is_date'];
				$is_number = $attribute['Definition']['atd_is_number'];
				$is_currency = $attribute['Definition']['atd_is_currency'];
				$is_editable = $attribute['Definition']['atd_is_editable'];
				if($is_image){
					$value .=  $this->_image($attribute['Attribute']['att_content'], 42, 38);
				}else
				if($is_data){
					$model = $attribute['Definition']['atd_data_model'];
					$data_key = $attribute['Definition']['atd_data_key'];
					$value .= $this->_data($model, $data_key, $attribute['Attribute']['att_content']);
				}else
				if($is_date){
					//debug("date");
					$value .= $this->_date($attribute['Attribute']['att_content']);
				}else
				if($is_currency)
					$value .= CakeNumber::currency($attribute['Attribute']['att_content']);
				else
					$value .= $attribute['Attribute']['att_content']; 
				$child['value'] = $value;
				$children[] = $child;
			}
			$item['children'] = $children;
			$items[] = $item;
			
			$identifier = "id";
			$idAttribute = "id";
			$label = 'name';
			$data = compact("identifier", "idAttribute", "label", "items");
			
			$this->log("items=" . var_export($items, true), 'debug');
			$this->log("</info id=$id>", 'debug');
			if(!empty($this->request->params['requested'])){
				return $data;
			}
			$this->set($data);
			$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
		}catch(Exception $e){
			$this->log($e->getMessage());
		}
	}
	
	private function _date($datetime){
		return date("F j, Y", $datetime);
	}
	
	private function _image($image_id, $width=50, $height=50, $options = array()){
		$attrs = "";
		foreach($options as $name => $value)
			$attrs .= $name . '="' . $value . '" ';
		if($image_id)
			return sprintf("<img src='/images/image?id=%d&width=%d&height=%d' width='%d' height='%d' %s>", 
					$image_id, $width, $height, $width, $height, $attrs);
		return sprintf("<img src='/img/pixel.gif' width='%d' height='%d'>", $image_id, $width, $height);
	}
	
	private function _data($model, $field = 'id', $value, $conditions = false, $displayField = ''){
		$controller = Inflector::underscore($model);
		$controller = Inflector::pluralize($controller);
		$action = "autocomplete";
		$use_codes = 0;
		if($field !== 'id')
			$use_codes = 1;
		$url = "/$controller/autocomplete/${use_codes}/$displayField";
		$map = $this->requestAction($url);
		
		if(!$map)
			return "";
		foreach($map as $key => $display){
			if($key === $value){
				return $display;
			}
		}
		return "";
	}
	
	/**
	 //build the following array
		array(
		    	"identifier" => "id",
		        "idAttribute" => "id",
			"label" => "name",
			"items" => array({
							model => 'pro',
							id,
							icon,
							name,
							value,
							ranking,
							club,
							children => [
								{
									model => 'comment',
									id,
									icon,
									player_name,
									match_name,
									player_icon,
									body,
									time
								 }
							]
						 }
					)
	 */
	public function comments($id = null, $max_id = 0, $date = 0){
		$this->viewClass = "Json";
		try{
			$date = intval($date/1000); //only seconds are considered in PHP
			$pro = $this->requestAction("/pros/view/$id");
			$items = array();
			$item = array();
			$item["model"] = "pro";
			$item["id"] = $id;
			$item["name"] = $pro["ProPerson"]["per_first_name"] . " " . $pro["ProPerson"]["per_last_name"];
			$item["short_name"] = $this->truncate($item['name'], 15);
			$item["icon"] = "/images/image?id=" .  $pro["ProPerson"]["per_img_id"] . "&width=32&height=32";
			$item["value"] = "USD$0";
			$item["ranking"] = 0;
			$item["club"] = $pro["ProClub"]["clb_name"];
			$children = array();
			
			$comments = $this->requestAction("/pros/comments/$id/0/${max_id}/$date");
			$this->log("comments:=" . var_export($comments, true), 'debug');
			
			foreach($comments as $comment){
				$com_id = $comment["Comment"]["id"];
				if($max_id & $com_id <= $max_id) continue;
				
				$raw_time = $comment['ComMatch']['mat_start_date'] + 
						$comment['Comment']['com_time']*1000;
				if($raw_time <= $date) continue;
				$child = array();
				$child['model'] = 'comment';
				$child['raw_time'] = $raw_time; 
				$child['com_time'] = $comment['Comment']['com_time']; 
				$child['time'] = $this->_formatTime($comment['ComMatch']['mat_start_date'] + 
						$comment['Comment']['com_time']*1000); 
				$child['icon' ] = $comment['Comment']['com_type'] . "-icon " . $comment['Comment']['com_type'];
				$child['com_type'] = $comment['Comment']['com_type'];
				
				$child['id'] = 'com' . $com_id ;
				$child['raw_id'] = $com_id ;
				$child['match_name'] = $comment['MatHostClub']['clb_name'] . " # " .  
							$comment['MatGuestClub']['clb_name'] . " (" . 
							$comment['MatChampionship']['cha_name'] .' ' .
							$comment['MatChampionshipYear']['chy_year'] . ')';
				$child['player_icon'] = "/images/image?id=" . $comment['ComPerson']['per_img_id'] . "&width=32&height=32";
				$child['player_name'] = $comment['ComPerson']['per_first_name'] . ' ' . $comment['ComPerson']['per_last_name'];
				$child['body'] = $this->_comment_body($comment['Comment']['id'], $comment['Comment']['com_type']);
				$child['since'] = __("since") . ' ' . $this->_formatTime($comment['ComMatch']['mat_start_date'] +
													$comment['Comment']['com_time']*1000) . ' ' . __("ago");
				$children[] = $child;
			}
			$item['children'] = $children;
			
			$items[] = $item;
			
			$identifier = "id";
			$idAttribute = "id";
			$label = 'name';
			$data = compact("identifier", "idAttribute", "label", "items");
			if(!empty($this->request->params['requested'])){
				return $data;
			}
			$this->set($data);
			$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
		}catch(Exception $e){
			$this->log($e->getMessage());
		}
	}
	
	private function _comment_body($comment_id = null, $comment_type = ""){
		$body = '';
		if($comment_type === 'attempt'){
			$attempt = $this->requestAction("/comments/attempt/${comment_id}");
			if(isset($attempt['AttType'])){
				$body .= '<span class="attempt-type"><span class="footnation-font goal-type-icon goal-' .  
							strtolower($attempt['AttType']['got_code']) . 
							'-icon" title="' . __(trim($attempt['AttType']['got_display_name'])) . '"></span>' .
					__(trim($attempt['AttType']['got_display_name'])) .
				'</span>';
			}
			
			$body .= '<span class="footnation-font goal-distance">' .
				$attempt['Attempt']['att_distance'] . ' ' . __("meters") .
			'</span>';
			
			if($attempt['Attempt']['att_is_on_target']){
				$body .= '<span class="attempt-on-target"></span>';
			}
		}else
		if($comment_type === 'goal'){
			$goal = $this->requestAction("/comments/goal/${comment_id}");
			if(isset($goal['GoaType'])){
				$body .= '<span class="goal-type">
					<span class="footnation-font goal-type-icon goal-' .  strtolower($goal['GoaType']['got_code']) . 
							'-icon" title="' . __(trim($goal['GoaType']['got_display_name'])) . '"></span>' .
					__(trim($goal['GoaType']['got_display_name'])) .
				'</span>' ;
			}
			
			$body .= '<span class="footnation-font goal-distance">' .  $goal['Goal']['goa_distance'] . " " . __("meters").' </span>' . 
			'<span class="footnation-font goal-passer-player">' .
				$goal['GoaPasserPerson']['per_first_name'] . '(' . $goal['GoaPasserPerson']['per_last_name'] . ')' . 
			'</span>';
		}else
		if($comment_type === 'top_catch'){
			$top_catch = $this->requestAction("/comments/top_catch/${comment_id}");
			if(isset($top_catch['CatType'])){
				$body .= '<span class="goal-type">
					<span class="footnation-font goal-type-icon goal-' 
						.  strtolower($top_catch['CatType']['got_code']) 
						. '-icon title="' . __(trim($top_catch['CatType']['got_display_name'])) . '"></span>' ;
				$body .=	__(trim($top_catch['CatType']['got_display_name'])) ;
			}
			if(isset( $top_catch['TopCatch'])){
			$body .= '</span>' .
			'<span class="footnation-font goal-distance">' .  $top_catch['TopCatch']['cat_distance'] . " " . __("meters").' </span>' ;
			}
		}else
		if($comment_type === 'image'){
			$body .= '<img src="/images/image/' . $comment['Comment']['com_img_id'] . '" width=42 height=38>';
		}else
		if($comment_type === 'replacement'){
			$replacement = $this->requestAction("/comments/replacement/${comment_id}");
			$body .= '<span class="replacement-replaced-player comment-player footnation-font">
				<span class="comment-player-icon" title="' . __("Replacement") . '"></span>' .
					'<img src="/images/image?id=' . $replacement['RepReplacedPerson']['per_img_id'] . '&width=32&height=32">' .
				'</span>
				<span class="comment-player-name">' .
					$replacement['RepReplacedPerson']['per_first_name'] . ' ' . $replacement['RepReplacedPerson']['per_last_name'] .
				'</span> 
				</span>';
		}else
		if($comment_type === 'text'){
			$body .= $comment['Comment']['com_text'];
		}
		return $body;
	}
	
	private function _comment_icon_text($comment_id = null, $comment_type = ""){
		$icon_text = '';
		if($comment_type === 'attempt'){
			$icon_text = "j";
		}else
		if($comment_type === 'goal'){
			$icon_text = "l";
		}else
		if($comment_type === 'top_catch'){
			$icon_text = "k";
		}else
		if($comment_type === 'image'){
			$icon_text = "c";
		}else
		if($comment_type === 'replacement'){
			$icon_text = "u";
		}
		return $icon_text;
	}
	
	
	/**
	//build the following array
		array(
		    	"identifier" => "id",
		        "idAttribute" => "id",
			"label" => "name",
			"items" => array({
					model => 'pro',
					id,
					icon,
					name,
					value,
					ranking,
					club,
					children => array(
						{
							model => 'stat',
							id,
							label,
							series => [{caption, name}],
							data => []
						 }
					)
				 })
	 */
	public function stats($id = null){
		$this->viewClass = "Json";
		$error = '';
		try{
			$pro = $this->requestAction("/pros/view/$id");
			$items = array();
			$item = array();
			$item["model"] = "pro";
			$item["id"] = $id;
			$item["name"] = $pro["ProPerson"]["per_first_name"] . " " . $pro["ProPerson"]["per_last_name"];
			$item["short_name"] = $this->truncate($item['name'], 15);
			$item["icon"] = "/images/image?id=" .  $pro["ProPerson"]["per_img_id"] . "&width=32&height=32";
			$item["value"] = "USD$0";
			$item["ranking"] = 0;
			$item["club"] = $pro["ProClub"]["clb_name"];
			$children = array();
			
			$child = array();
			$child['model'] = 'stat';
			$child['id'] = "sta1";
			$child['label'] =  __("Goals per Month");
			$series = array(
				array(
					"caption" => __("Attempt"),
					"column" => "attempts"
				),
				array(
					"caption" => __("Goals"),
					"column" => "goals"
				),
				array(
					"caption" => __("All"),
					"column" => "all"
				)
			);
			$child['series'] = json_encode($series);
			$data = $this->requestAction("/pros/goals_per_month/$id");
			$child['data']= json_encode($data);
			$children[] = $child;
			
			$child['model'] = 'stat';
			$child['id'] = 'sta2';
			$child['label'] = __("Cards Per Month");
			$series = array(
				array(
					"caption" => __("Yellows"),
					"column" => "yellows"
				),
				array(
					"caption" => __("Reds"),
					"column" => "reds"
				)
			);
			$child['series'] = json_encode($series);
			$data = $this->requestAction("/pros/cards_per_month/$id");
			$child['data']= json_encode($data);
			$children[] = $child;
			
			$child['model'] = 'stat';
			$child['id'] = 'sta3';
			$child['label'] = __("Goal  Per Types");
			$series = array(
				array(
					"caption" => __("Attempts"),
					"column" => "attempts"
				),
				array(
					"caption" => __("Goals"),
					"column" => "goals"
				),
				array(
					"caption" => __("All"),
					"column" => "all"
				)
			);
			$child['series'] = json_encode($series);
			$data = $this->requestAction("/pros/goal_per_types/$id");
			$child['data']= json_encode($data);
			$children[] = $child;
			
			/*$child['model'] = 'stat';
			$child['id'] = 'sta4';
			$child['label'] = __("Game Time Per Month");
			$series = array(
				array(
					"caption" => __("Game Time"),
					"column" => "game_time"
				),
				array(
					"caption" => __("Total"),
					"column" => "total"
				)
			);
			$child['series'] = json_encode($series);
			$child['data'] = $this->requestAction("/pros/game_time_per_month/$id");
			$children[] = $child;*/
			
			$item['children'] = $children;
			
			$items[] = $item;
			$identifier = "id";
			$idAttribute = "id";
			$label = 'name';
			$data = compact("identifier", "idAttribute", "label", "items");
			if(!empty($this->request->params['requested'])){
				return $data;
			}
			$this->set($data);
			$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
		}catch(Exception $e){
			$this->log($e->getMessage());
		}
	}
	
	/**
	//build the following array
		array(
		    	"identifier" => "id",
		        "idAttribute" => "id",
			"label" => "name",
			"items" => array(model => 'pro',
							id,
							icon,
							name,
							value,
							ranking,
							club,
							children => array(
								{
									model => 'news',
									id,
									avatar,
									name,
									created_at,
									user,
									text
								 }
							)
		)
	 */
	public function news($id = null, $since_id = 0, $max_id = 0){
		if(isset($this->request->query['max_id']))
			$max_id = $this->request->query['max_id'];
		if(isset($this->request->query['since_id']))
			$since_id = $this->request->query['since_id'];
		$this->log("<news id=$id since_id=${since_id} max_id=${max_id}>", 'debug');
		$this->viewClass = "Json";
		$error = "";
		try{
			$items = array();
			$pro = $this->requestAction("/pros/view/$id");
	
			$item = array();
			$item["model"] = "pro";
			$item["id"] = $id;
			$item["name"] = $pro["ProPerson"]["per_first_name"] . " " . $pro["ProPerson"]["per_last_name"];
			$item["short_name"] = $this->truncate($item['name'], 15);
			$item["icon"] = "/images/image?id=" .  $pro["ProPerson"]["per_img_id"] . "&width=32&height=32";
			$item["value"] = "USD$0";
			$item["ranking"] = 0;
			$item["club"] = $pro["ProClub"]["clb_name"];
			$children = array();
			
			$account = $pro['ProPerson']['per_twitter_account'];
			$this->log("twitter account=$account", 'debug');
			if(!$account){
				$error = __("No twitter account found");
				throw new Exception($error);
			}
			
			$Twitter_statuses = Configure::read('Twitter.statuses');
			$connection = $this->__getTwitterConnection();
			$numtweets = 5;
			$Twitter_statuses = sprintf($Twitter_statuses, $account, $numtweets);
			if($max_id){
				$Twitter_statuses .= "&max_id=" . $max_id;
			}else
			if($since_id){
				$Twitter_statuses .= "&since_id=" . $since_id;
			}
			$this->log("Twitter_statuses=${Twitter_statuses}", 'debug');
			$tweets = $connection->get($Twitter_statuses);
			$this->log("twitter response=" . var_export($tweets, true), 'debug');
			if(isset($tweets->errors) && $tweets->errors[0]->message != ""){
				$error = __("Unable to collect tweets\r\n");
				throw new Exception($error);
			}else
			if($tweets){
				$user_name  = "";
				if(!empty($tweets))
					$user_name = $tweets[0]->user->screen_name;
				foreach($tweets as $tweet){
					//add tweets
					$child = array();
					$child['model'] = 'tweet';
					$child['text'] = $this->_formatTweet($tweet->text);
					$child['user'] = $user_name;
					$child['name'] = $tweet->user->name? $tweet->user->name:  $user_name;
					$child['avatar'] = $tweet->user->profile_image_url? $tweet->user->profile_image_url
										: "";
					$child['time'] = $this->_formatTime($tweet->created_at);
					$child['created_at'] = $tweet->created_at;
					$child['id'] = $tweet->id_str;
					$children[] = $child;
				}
			}
			
			$item['children'] = $children;
			$items[] = $item;
		}catch(Exception $e){
			$this->log($e->getMessage());
		}
		$identifier = "id";
		$idAttribute = "id";
		$label = 'name';
		$data = compact("error", "identifier", "idAttribute", "label", "items");
		if(!empty($this->request->params['requested'])){
			return $data;
		}
		$this->set($data);
		$this->set("_serialize", array("error", "identifier", "idAttribute", "label", "items"));
		$this->log("</news>", 'debug');
	}
	
	private function _formatTweet($tweetText = '') {
		$tweetText = preg_replace("/(https?:\/\/\S+)/i", '<a href="$1">$1</a>', $tweetText);
		$tweetText = preg_replace("/(^|\s)@(\w+)/",  '$1<a href="http://twitter.com/$2">@$2</a>', $tweetText);
		$tweetText = preg_replace("/(^|\s)#(\w+)/", '$1<a href="http://search.twitter.com/search?q=%23$2">#$2</a>', $tweetText);
		return $tweetText;
	}
	
	private function format_plural($val, $string_singular = "", $string_plural = ""){
		if($val <= 0)
			return "";
		if($val == 1)
			return  $val . ' ' . $string_singular;
		return  $val . ' ' . $string_plural;
	}
	
	// Formats the time as received by Twitter
	private function _formatTime($d = null) {
		// Get now
		
		$now = new DateTime();
		
		// Push string date into an Date object
		$tweetDate = new Datetime();
		$tweetDate->setTimestamp($d);
		$date_diff = $now->diff($tweetDate);
		if($date_diff){ 
			$years = $date_diff->y; 
			$months = $date_diff->m;
			$days = $date_diff->d; 
			$hours = $date_diff->h;
			$mins = $date_diff->i;
			$secs = $date_diff->s;
		}
		
		$date_diff_str = $this->format_plural($years, __('year'),  __('years')) . ' ' .
					$this->format_plural($months,  __('month'),  __('months')) . ' ' .
					$this->format_plural($days,  __('day'), __('days'));
		if(!$date_diff_str)
				$date_diff_str .= $this->format_plural($hours,  __('hour'), __('hours')) . ' ' .
					$this->format_plural($mins,  __('min'), __('mins'))  . ' ' ;
		//what if the comment was just posted a few seconds ago?
		if(!$date_diff_str) //then show the seconds
			$date_diff_str = format_plural($secs, __('sec'), __('secs'));
		
		return trim($date_diff_str);
	}
	
	private function __getTwitterConnection(){
		$consumerkey = "8HJ0TeEFu7iQkKv7UumtA";
		$consumersecret = "oyqvDbYtnxsW2vGWAyvzyHOTdnTvfrAlZCn97japU";
		$accesstoken = "2341659240-eLbHtmtKvuOovEGGg1nx9sMsIFR38IEXAHexXbi";
		$accesstokensecret = "QKf3lfY9RgM0qvrM5iqFOs2CMbzg3ngRYmKwqSFpdz3He";
 
		function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
			$connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
			return $connection;
		}
 
		$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
		
		return $connection;
	}
}
