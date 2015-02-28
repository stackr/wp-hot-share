<?php
class Hot_Share{
	function __construct(){
		$this->sns_share = array(
				'facebook'		=> __('Facebook','hotpack'),
				'twitter'		=> __('Twitter','hotpack'),
				'googleplus'	=> __('Google Plus','hotpack'),
				'kakaotalk'		=> __('Kakao Talk','hotpack'),
				'kakaostory'	=> __('Kakao Story','hotpack'),
				'line'			=> __('Line','hotpack')
			);
		
		add_action( 'wp_head',array(&$this, 'wp_head'));
		add_action( 'admin_menu', array(&$this,'admin_menu') );
		add_action( 'admin_init', array(&$this,'register_setting') );
		add_filter( 'the_content', array(&$this,'the_content') );
		add_action( 'wp_enqueue_scripts', array(&$this,'wp_enqueue_scripts'));
		
	}
	function wp_head(){

		$hot_share_location = get_option('hot_share_location',true);
		$this->show = false;
		if( is_singular() && in_array( get_post_type(), $hot_share_location ) ){
			$this->show = true;
		}else if(in_array('archive',$hot_share_location) && (is_home() || is_archive())){
			$this->show = true;
		}
		$hot_share = get_option('hot_share',true);
		foreach($this->sns_share as $id => $name){
			if($id == 'googleplus'){
				add_action('wp_footer',array(&$this,'googleplust_footer'));
			}
		}
	}
	function admin_menu(){
		add_options_page( __('Hot Share','hotpack'), __('Hot Share','hotpack'), 'activate_plugins', 'hotshare', array(&$this,'hot_share_option_page') );
	}
	function register_setting(){
		register_setting('hot-share','hot_share');
		register_setting('hot-share','hot_share_location');
	}
	function hot_share_option_page(){
		
		?>
		<div class="wrap">
			<h2><?php _e('Hot Share','hotpack');?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( 'hot-share' );?>
				<?php
				$hot_share = get_option('hot_share',true);
				
				$hot_share_location = get_option('hot_share_location',true);
				?>
				<h3 class="title"><?php _e('사용할 공유서비스 선택','hotpack');?></h3>
				<table class="form-table">
					<tbody>
						<?php foreach($this->sns_share as $id => $name):?>
						<?php 
						$selected = empty($hot_share[$id]) ? 'N' : $hot_share[$id];
						?>
						<tr>
							<th scope="row"><label for="<?php echo $id;?>"><?php echo $name;?></label></th>
							<td>
								<select name="hot_share[<?php echo $id;?>]" type="text" id="<?php echo $id;?>" class="postform">
									<option value='Y'<?php selected( $selected, 'Y' );?>><?php _e('사용함','hotpack');?></option>
									<option value='N'<?php selected( $selected, 'N' );?>><?php _e('사용안함','hotpack');?></option>
								</select>
								<?php if($id == 'kakaotalk'):?>
								<?php $kakaoapi = isset($hot_share['kakaoapi']) ? $hot_share['kakaoapi'] : '';?>
								<br/><label for="kakaoapi"><?php echo __('Kakao Javascript API : ','hotpack');?><input type="text" name="hot_share[kakaoapi]" value="<?php echo $kakaoapi;?>" id="kakaoapi">
								<?php endif;?>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<h3 class="title"><?php _e('공유서비스 설정','hotpack');?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><lable for="shareon"><?php _e('공유서비스 표시위치','hotpack');?></th>
							<td>
								<!--<label><input type="checkbox" name="hot_share_location[]" id="archive" value="archive"/><?php _e('Front Page, Archive Page');?></label></br>-->
								<?php
								
								$post_types = get_post_types( array( 'public' => true ) );
								foreach($post_types as $post_type => $name){
									?><label><input type="checkbox" name="hot_share_location[]" id="<?php echo $post_type;?>" value="<?php echo $post_type;?>"<?php if(in_array($post_type,$hot_share_location)){echo " checked";}?>/><?php _e($name);?></label></br><?php
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button();?>
			</form>
		</div>
		<?php
	}
	function the_content($content){
		$share = "";
		$this->post_title = get_the_title();
		$this->post_permalink = get_permalink();
		if($this->show){
			$share .= "<div id='hot-share-container'><ul id='hot-share-ul'>";
			$hot_share = get_option('hot_share',true);
			foreach($this->sns_share as $id => $name){

				switch($id){
					case 'facebook':
						$share .= $this->facebook();
						break;
					case 'twitter':
						$share .= $this->twitter();
						break;
					case 'googleplus':
						$share .= $this->googleplus();
						break;
					case 'kakaotalk':
						$share .= $this->kakaotalk();
						break;
					case 'kakaostory':
						$share .= $this->kakaostory();
						break;
					case 'line':
						$share .= $this->line();
						break;
				}

			}
			$share .= "</ul></div>";
		}
		return $content.$share;
	}
	
	function img_url($filename){
		return plugins_url( 'images/'.$filename, __FILE__);
	}
	function facebook(){
		$url = $this->http() . '://www.facebook.com/sharer.php?u=' . rawurlencode( $this->post_permalink ) . '&t=' . rawurlencode( $this->post_title );
		//$url = '//www.facebook.com/plugins/like.php?href=' . rawurlencode( $this->post_permalink ) . '&amp;layout=button_count&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;height=21';
		//return '<div class="like_button"><iframe src="'.$url.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe></div>';

		return '<li><a href="'.$url.'"><img src="'.$this->img_url('sns_share__facebook.png').'" alt="'.__('Share Facebook','hotpack').'" target="_blank"></a></li>';
	}
	function twitter(){
		//https://twitter.com/intent/tweet?original_referer=home_url();&text=%EC%98%AC%ED%95%B4+%ED%81%AC%EB%A6%AC%EC%8A%A4%EB%A7%88%EC%8A%A4%EB%A7%88%EC%A0%80+%E2%80%98%EB%82%98+%ED%99%80%EB%A1%9C%E2%80%99%3F&url=rawurlencode( $this->post_permalink )
		$url = $this->http(). '://twitter.com/intent/tweet?original_referer='.home_url().'&text='.rawurlencode( $this->post_title ).'&url='.rawurlencode( $this->post_permalink );
		return '<li><a href="'.$url.'"><img src="'.$this->img_url('sns_share__twitter.png').'" alt="'.__('Share Twitter','hotpack').'" target="_blank"></a></li>';
		////return '<div class="twitter_button"><iframe allowtransparency="true" frameborder="0" scrolling="no" src="' . esc_url( '//platform.twitter.com/widgets/tweet_button.html?url=' . rawurlencode( $this->post_permalink ) . '&counturl=' . rawurlencode( str_replace( 'https://', 'http://', $this->post_permalink ) ) . '&count=horizontal&text=' . rawurlencode( $this->post_title . ':' )  ) . '" style="width:101px; height:20px;"></iframe></div>';
	}
	function googleplus(){
		$url = 'https://plus.google.com/share?url=' . rawurlencode( $this->post_permalink );
		return '<li><a href="'.$url.'"><img src="'.$this->img_url('sns_share__googleplus.png').'" alt="'.__('Share Google Plus','hotpack').'" target="_blank"></a></li>';
		//return '<div class="googleplus1_button"><div class="g-plus" data-action="share" data-annotation="bubble" data-href="' . esc_url( $this->post_permalink ) . '"></div></div>';
	}
	function kakaotalk(){
		if(wp_is_mobile()){
			return '<li><a href="#sharekakaotalk" class="share_kakaotalk" onclick="share_kakaotalk(\''.$this->post_title.'\',\''.$this->post_permalink.'\');"><img src="'.$this->img_url('sns_share__kakaotalk.png').'" alt="'.__('Share kakaotalk','hotpack').'" target="_blank"></a></li>';
		}
	}
	function kakaostory(){
		return '<li><a href="#sharekakaostory" id="share-kakaostory" onclick="share_kakaostory(\''.$this->post_permalink.'\');"><img src="'.$this->img_url('sns_share__kakaostory.png').'" alt="'.__('Share kakaostory','hotpack').'" target="_blank"></a></li>';
	}
	function line(){
		if(wp_is_mobile()){
			$url = 'http://line.me/R/msg/text/?'.rawurlencode( $this->post_title ).'%0D%0A'.rawurlencode( $this->post_permalink );
			$this->http(). '://twitter.com/intent/tweet?original_referer='.home_url().'&text='.rawurlencode( $this->post_title ).'&url='.rawurlencode( $this->post_permalink );
			return '<li><a href="'.$url.'"><img src="'.$this->img_url('sns_share__line.png').'" alt="'.__('LINE it!','hotpack').'" target="_blank"></a></li>';
			/*return '<div class="line_button"><span>
<script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script>
<script type="text/javascript">
new media_line_me.LineButton({"pc":false,"lang":"en","type":"a"});
</script>
</span></div>';*/
		}
	}
	function googleplust_footer(){
		?>
		<script type="text/javascript">
		  (function() {
		    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		    po.src = 'https://apis.google.com/js/plusone.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>
		<?php
	}
	function kakaotalk_footer(){
		?>
		<script type='text/javascript'>
	    Kakao.init('14e567c494027f9d4c54d8975393abee');

	    // 카카오톡 링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
	    Kakao.Link.createTalkLinkButton({
	      container: '#kakao-link-btn',
	      label: '카카오톡으로 공유하기',
	      webButton: {
	        text: '<?php echo $this->post_title;?>',
	        url: '<?php echo $this->post_permalink;?>'
	      }
	    });
	    </script>
		<?php
	}
	function kakaostory_footer(){
	?>

	<?php
	}
	public function http() {
		return is_ssl() ? 'https' : 'http';
	}
	function wp_enqueue_scripts(){
		$hot_share = get_option('hot_share',true);
		$kakaoapi = isset($hot_share['kakaoapi']) ? $hot_share['kakaoapi'] : '';
		wp_enqueue_script( 'kakao-api', '//developers.kakao.com/sdk/js/kakao.min.js', false );
		wp_register_script( 'hot-share', plugins_url( '_asset/js/hot-share.js', dirname(__FILE__) ), array('jquery','kakao-api') , '1.0');
		wp_localize_script( 'hot-share', 'HotShare', array('kakaoAPI'=>$kakaoapi) );
		wp_enqueue_script( 'hot-share' );
		wp_enqueue_style( 'hot-share', plugins_url( '_asset/css/hot-share.css', dirname(__FILE__) ), false, '1.0');
	}
}
?>