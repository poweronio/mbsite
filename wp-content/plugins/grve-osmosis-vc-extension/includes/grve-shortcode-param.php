<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'add_shortcode_param' ) ) {

	function grve_osmosis_vce_icon_settings_field( $param, $param_value ) {
		$dependency = vc_generate_dependencies_attributes( $param );

		$subset = $subset_class = '';
		if( isset( $param['param_subset'] ) ) {
			$subset = $param['param_subset'];
			$subset_class = 'grve-subset';
		}

		return '<div class="grve-modal-select-icon-container ' . $subset_class . '">'
				. grve_get_awsome_fonts_icons( $subset ) .
				'</div>' .
				'<input type="hidden" name="' . $param['param_name'] . '" id="grve-icon-field" class="wpb_vc_param_value grve-modal-textfield' . $param['param_name'] . ' ' . $param['type'] . '_field" value="' . $param_value . '" ' . $dependency . '/>'
				;

	}
	add_shortcode_param( 'grve_icon', 'grve_osmosis_vce_icon_settings_field', GRVE_OSMOSIS_VC_EXT_PLUGIN_DIR_URL . '/assets/js/grve-icon-preview.js' );
	
	function grve_osmosis_vce_multi_checkbox_settings_field( $param, $param_value ) {
		$dependency = vc_generate_dependencies_attributes( $param );
		
		$param_line = '';
		$current_value = explode(",", $param_value);
		$values = is_array($param['value']) ? $param['value'] : array();
				
		foreach ( $values as $label => $v ) {
			$checked = in_array($v, $current_value) ? ' checked="checked"' : '';
			$checkbox_input_class = 'grve-checkbox-input-item';
			$checkbox_class = 'grve-checkbox-item';
			if ( '' == $v ) {
				$checkbox_input_class = 'grve-checkbox-input-item-all';
				$checkbox_class = 'grve-checkbox-item grve-checkbox-item-all';
			}
			$param_line .= '<div class="' . $checkbox_class . '"><input id="'. $param['param_name'] . '-' . $v .'" value="' . $v . '" class="'. $checkbox_input_class .'" type="checkbox" '.$checked.'> ' . __($label, "js_composer") . '</div>';
		}
				
		return '<div class="grve-multi-checkbox-container">' .
			   '  <input class="wpb_vc_param_value wpb-checkboxes '.$param['param_name'].' '.$param['type'].'_field" type="hidden" value="'.$param_value.'" name="'.$param['param_name'].'"/>'
				. $param_line .
				'</div>';

	}
	add_shortcode_param( 'grve_multi_checkbox', 'grve_osmosis_vce_multi_checkbox_settings_field', GRVE_OSMOSIS_VC_EXT_PLUGIN_DIR_URL . '/assets/js/grve-multi-checkbox.js' );	
	
}

if ( ! function_exists( 'grve_get_awsome_fonts_icons' ) ) {

	function grve_get_awsome_fonts_icons( $subset = '' ) {
		//Font Awesome 4.3.0
		if ( empty( $subset ) ) {
			$grve_awsome_fonts = array( "adjust", "adn", "align-center", "align-justify", "align-left", "align-right", "ambulance", "anchor", "android", "angellist", "angle-double-down", "angle-double-left", "angle-double-right", "angle-double-up", "angle-down", "angle-left", "angle-right", "angle-up", "apple", "archive", "area-chart", "arrow-circle-down", "arrow-circle-left", "arrow-circle-o-down", "arrow-circle-o-left", "arrow-circle-o-right", "arrow-circle-o-up", "arrow-circle-right", "arrow-circle-up", "arrow-down", "arrow-left", "arrow-right", "arrows", "arrows-alt", "arrows-h", "arrows-v", "arrow-up", "asterisk", "at", "automobile", "backward", "ban", "bank", "bar-chart", "bar-chart-o", "barcode", "bars", "bed", "beer", "behance", "behance-square", "bell", "bell-o", "bell-slash", "bell-slash-o", "bicycle", "binoculars", "birthday-cake", "bitbucket", "bitbucket-square", "bitcoin", "bold", "bolt", "bomb", "book", "bookmark", "bookmark-o", "briefcase", "btc", "bug", "building", "building-o", "bullhorn", "bullseye", "bus", "buysellads", "cab", "calculator", "calendar", "calendar-o", "camera", "camera-retro", "car", "caret-down", "caret-left", "caret-right", "caret-square-o-down", "caret-square-o-left", "caret-square-o-right", "caret-square-o-up", "caret-up", "cart-arrow-down", "cart-plus", "cc", "cc-amex", "cc-discover", "cc-mastercard", "cc-paypal", "cc-stripe", "cc-visa", "certificate", "chain", "chain-broken", "check", "check-circle", "check-circle-o", "check-square", "check-square-o", "chevron-circle-down", "chevron-circle-left", "chevron-circle-right", "chevron-circle-up", "chevron-down", "chevron-left", "chevron-right", "chevron-up", "child", "circle", "circle-o", "circle-o-notch", "circle-thin", "clipboard", "clock-o", "close", "cloud", "cloud-download", "cloud-upload", "cny", "code", "code-fork", "codepen", "coffee", "cog", "cogs", "columns", "comment", "comment-o", "comments", "comments-o", "compass", "compress", "connectdevelop", "copy", "copyright", "credit-card", "crop", "crosshairs", "css3", "cube", "cubes", "cut", "cutlery", "dashboard", "dashcube", "database", "dedent", "delicious", "desktop", "deviantart", "diamond", "digg", "dollar", "dot-circle-o", "download", "dribbble", "dropbox", "drupal", "edit", "eject", "ellipsis-h", "ellipsis-v", "empire", "envelope", "envelope-o", "envelope-square", "eraser", "eur", "euro", "exchange", "exclamation", "exclamation-circle", "exclamation-triangle", "expand", "external-link", "external-link-square", "eye", "eyedropper", "eye-slash", "facebook", "facebook-f", "facebook-official", "facebook-square", "fast-backward", "fast-forward", "fax", "female", "fighter-jet", "file", "file-archive-o", "file-audio-o", "file-code-o", "file-excel-o", "file-image-o", "file-movie-o", "file-o", "file-pdf-o", "file-photo-o", "file-picture-o", "file-powerpoint-o", "files-o", "file-sound-o", "file-text", "file-text-o", "file-video-o", "file-word-o", "file-zip-o", "film", "filter", "fire", "fire-extinguisher", "flag", "flag-checkered", "flag-o", "flash", "flask", "flickr", "floppy-o", "folder", "folder-o", "folder-open", "folder-open-o", "font", "forumbee", "forward", "foursquare", "frown-o", "futbol-o", "gamepad", "gavel", "gbp", "ge", "gear", "gears", "genderless", "gift", "git", "github", "github-alt", "github-square", "git-square", "gittip", "glass", "globe", "google", "google-plus", "google-plus-square", "google-wallet", "graduation-cap", "gratipay", "group", "hacker-news", "hand-o-down", "hand-o-left", "hand-o-right", "hand-o-up", "hdd-o", "header", "headphones", "heart", "heartbeat", "heart-o", "history", "home", "hospital-o", "hotel", "h-square", "html5", "ils", "image", "inbox", "indent", "info", "info-circle", "inr", "instagram", "institution", "ioxhost", "italic", "joomla", "jpy", "jsfiddle", "key", "keyboard-o", "krw", "language", "laptop", "lastfm", "lastfm-square", "leaf", "leanpub", "legal", "lemon-o", "level-down", "level-up", "life-bouy", "life-buoy", "life-ring", "life-saver", "lightbulb-o", "line-chart", "link", "linkedin", "linkedin-square", "linux", "list", "list-alt", "list-ol", "list-ul", "location-arrow", "lock", "long-arrow-down", "long-arrow-left", "long-arrow-right", "long-arrow-up", "magic", "magnet", "mail-forward", "mail-reply", "mail-reply-all", "male", "map-marker", "mars", "mars-double", "mars-stroke", "mars-stroke-h", "mars-stroke-v", "maxcdn", "meanpath", "medium", "medkit", "meh-o", "mercury", "microphone", "microphone-slash", "minus", "minus-circle", "minus-square", "minus-square-o", "mobile", "mobile-phone", "money", "moon-o", "mortar-board", "motorcycle", "music", "navicon", "neuter", "newspaper-o", "openid", "outdent", "pagelines", "paint-brush", "paperclip", "paper-plane", "paper-plane-o", "paragraph", "paste", "pause", "paw", "paypal", "pencil", "pencil-square", "pencil-square-o", "phone", "phone-square", "photo", "picture-o", "pie-chart", "pied-piper", "pied-piper-alt", "pinterest", "pinterest-p", "pinterest-square", "plane", "play", "play-circle", "play-circle-o", "plug", "plus", "plus-circle", "plus-square", "plus-square-o", "power-off", "print", "puzzle-piece", "qq", "qrcode", "question", "question-circle", "quote-left", "quote-right", "ra", "random", "rebel", "recycle", "reddit", "reddit-square", "refresh", "remove", "renren", "reorder", "repeat", "reply", "reply-all", "retweet", "rmb", "road", "rocket", "rotate-left", "rotate-right", "rouble", "rss", "rss-square", "rub", "ruble", "rupee", "save", "scissors", "search", "search-minus", "search-plus", "sellsy", "send", "send-o", "server", "share", "share-alt", "share-alt-square", "share-square", "share-square-o", "shekel", "sheqel", "shield", "ship", "shirtsinbulk", "shopping-cart", "signal", "sign-in", "sign-out", "simplybuilt", "sitemap", "skyatlas", "skype", "slack", "sliders", "slideshare", "smile-o", "soccer-ball-o", "sort", "sort-alpha-asc", "sort-alpha-desc", "sort-amount-asc", "sort-amount-desc", "sort-asc", "sort-desc", "sort-down", "sort-numeric-asc", "sort-numeric-desc", "sort-up", "soundcloud", "space-shuttle", "spinner", "spoon", "spotify", "square", "square-o", "stack-exchange", "stack-overflow", "star", "star-half", "star-half-empty", "star-half-full", "star-half-o", "star-o", "steam", "steam-square", "step-backward", "step-forward", "stethoscope", "stop", "street-view", "strikethrough", "stumbleupon", "stumbleupon-circle", "subscript", "subway", "suitcase", "sun-o", "superscript", "support", "table", "tablet", "tachometer", "tag", "tags", "tasks", "taxi", "tencent-weibo", "terminal", "text-height", "text-width", "th", "th-large", "th-list", "thumbs-down", "thumbs-o-down", "thumbs-o-up", "thumbs-up", "thumb-tack", "ticket", "times", "times-circle", "times-circle-o", "tint", "toggle-down", "toggle-left", "toggle-off", "toggle-on", "toggle-right", "toggle-up", "train", "transgender", "transgender-alt", "trash", "trash-o", "tree", "trello", "trophy", "truck", "try", "tty", "tumblr", "tumblr-square", "turkish-lira", "twitch", "twitter", "twitter-square", "umbrella", "underline", "undo", "university", "unlink", "unlock", "unlock-alt", "unsorted", "upload", "usd", "user", "user-md", "user-plus", "users", "user-secret", "user-times", "venus", "venus-double", "venus-mars", "viacoin", "video-camera", "vimeo-square", "vine", "vk", "volume-down", "volume-off", "volume-up", "warning", "wechat", "weibo", "weixin", "whatsapp", "wheelchair", "wifi", "windows", "won", "wordpress", "wrench", "xing", "xing-square", "yahoo", "yelp", "yen", "youtube", "youtube-play", "youtube-square" );
		} else {
			$grve_awsome_fonts = array( "check", "angle-right", "angle-double-right", "circle", "square", "plus", "minus", "info-circle", "pencil" );
		}

		$options_number = count( $grve_awsome_fonts );
		$printable_awsome_fonts = "";

		for ( $i=0; $i < $options_number; $i++ ) {
			$printable_awsome_fonts .= '<i data-icon-value="' . $grve_awsome_fonts[ $i ] . '" class="grve-modal-icon-preview fa fa-' . $grve_awsome_fonts[ $i ] . '" title="' . $grve_awsome_fonts[ $i ] . '"></i>';
		}
		return $printable_awsome_fonts;
	}

}


?>