<?php
/* 
Plugin Name: Forum Haber Ekleyici 
Plugin URI: http://www.ubuntu-tr.net 
Description: Forumdaki iletinin sadece URL'sini girerek içeriğini almaya yarayan bir eklenti 
Version: 1.1.2
Author: İbrahim Altunok 
Author URI: http://www.ubuntu-tr.net 
License: GPLv2 
*/ 

add_action( 'add_meta_boxes', 'utr_forumhaber_kutu_ekle' );

// backwards compatible (before WP 3.0)
add_action( 'admin_init', 'utr_forumhaber_kutu_ekle', 1 );

function utr_forumhaber_kutu_ekle() { 
  add_meta_box( 
    'utr_forumhaber_kutu', 
    __( 'Forum İleti Bilgileri Alanı', 'utr_forumhaber_textdomain' ), 
    'utr_forumhaber_kutu_icerigi', 
    'post', 
    'normal', 
    'default' 
  ); 
}

function utr_forumhaber_kutu_icerigi( $post ) { 

  $utr_forumhaber_url = get_post_meta($post->ID, 'utr_forumhaber_url', true); 
  wp_nonce_field( plugin_basename( __FILE__ ), 'utr_forumhaber_noncename' ); 

  ?> 

  <div style="padding:10px 0px;"> 
    <label for="utr_forumhaber_url_alan">
      <?php _e("Bağlantı", "utr_forumhaber_textdomain" ); ?>
    </label> 

    <input type="text" id="utr_forumhaber_url" name="utr_forumhaber_url" value="<?php echo $utr_forumhaber_url;?>" size="50" /> 

    <input type="button" id="utr_forumhaber_parse" value="<?php _e("İçeriği Al","utr_forumhaber_textdomain");?>"> 

    <img src='images/wpspin_light.gif' id='utr_forumhaber_yukleniyor' style='display:none'> 
  </div> 

  <script type="text/javascript"> 
  
  (function($) { 

    if($("#utr_forumhaber_kutu")) $("#titlediv").prepend($("#utr_forumhaber_kutu")); 

  })(jQuery); 
  
  </script> 

  <?php 
} 

if ( in_array( $pagenow, array('post.php', 'post-new.php') ) ) { 
	add_action('admin_head', 'utr_forumhaber_js'); 
} 

function utr_forumhaber_js(){ 
  ?> 

  <script type="text/javascript"> 
  
  jQuery(document).ready(function($) { 
    $("#utr_forumhaber_parse").click(function(){ 
      $("#utr_forumhaber_yukleniyor").show(); 
      var data = { 
        action : 'utr_forumhaber_ayikla', 
        utrforumurl : $("#utr_forumhaber_url").val() 
      }; 
      $.get(ajaxurl, data, function(d){ 
        $("#title-prompt-text").hide(); 
        $("#title").val(d.baslik); 
        
        $("#content").html(d.ileti); 
        $("#content_ifr").contents().find("body").html(d.ileti); 
        
        $("#utr_forumhaber_yukleniyor").hide(); 
      },'json'); 
    }); 
  }); 
  
  </script>

<?php 
} 

add_action('wp_ajax_utr_forumhaber_ayikla', 'utr_forumhaber_ayikla');

function utr_forumhaber_ayikla() {
  if(!isset($_GET['utrforumurl']) || $_GET['utrforumurl'] == '') die();

  $data = '';
	
  if(extension_loaded("curl")){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $_GET['utrforumurl']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    $data = curl_exec($ch); 
 
  }

  else {
    $data = file_get_contents($_GET['utrforumurl']); 
  }

  preg_match('/msg(\d+)/', $_GET['utrforumurl'], $msgid);
  $msg = $msgid[1];
	
  preg_match('#<a id="msg' . $msg . '"></a>.*?windowbg.*?>(.*?)<hr class="post_separator" />#si', $data, $div);
  $div = $div[1];
  $div = preg_replace('#PHPSESSID=.*?&amp;#si', '', $div);
	
  preg_match('#action=profile;u=(.*?)".*?>(.*?)</a>#si', $div, $user);
  $username = $user[2];
  $userid = $user[1];
		
  preg_match('#<h5 id="subject_' . $msg . '">.*?<a.*?>(.*?)</a>.*?</h5>#si', $div, $title);
  $return['baslik'] = str_replace("Ynt: ","",$title[1]);
			
  preg_match('#&\#171;(.*?)&\#187;#si', $div, $date);
  $date = trim(preg_replace('#<strong>.*?</strong>#si', '', $date[1]));
  $date = preg_replace('#<b>.*?Bugün.*?</b>.*?,#si', date_i18n('d F Y') . " - ", $date);
	
  $r=substr($div,strpos($div,'<div class="inner" id="msg_'.$msg.'">'));
  $r2="";
  for($d=0; ; $d++) {
    $r2 = substr($r, 0, strpos($r, "</div>", $d) + 6);
    preg_match_all('#<div#si', $r2, $ad);
    preg_match_all('#</div#si', $r2, $kd);
    if(count($ad[0]) == count($kd[0])) break;
  }
  $r2=substr($r2,strpos($r2,">")+1);
  $r2=substr($r2,0,strrpos($r2,"</div>"));
  
  $r2 = preg_replace('#<div class="codeheader">(.*?)</div>#si', '<div class="codeheader">Kod:</div>', $r2);
	
  $r2 = $r2 . "<br /><br />Bu ileti <i>".$date."</i> tarihinde " .
    "<a href='http://forum.ubuntu-tr.net/index.php?action=profile;u=" .
    $userid . "'><i>" . $username . "</i></a> " .
    "tarafından yazılmıştır." . 
    "<br><a href='" . $_GET['utrforumurl'] . "' target='_blank'>" . 
    "İletiyi forumda açmak için tıklayınız »</a>";

  $return['ileti'] = $r2;
		
  echo json_encode($return);
  die();
}

add_action( 'save_post', 'utr_forumhaber_kaydet' );

function utr_forumhaber_kaydet( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return;

  if ( !wp_verify_nonce( $_POST['utr_forumhaber_noncename'], plugin_basename( __FILE__ ) ) )
    return;

  if ( 'post' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_post', $post_id ) )
      return;
  }
  else 
    return;
  
  update_post_meta($post_id, 'utr_forumhaber_url', $_POST['utr_forumhaber_url']);

}


