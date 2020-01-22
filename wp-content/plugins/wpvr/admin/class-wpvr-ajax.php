<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * The admin-specific Ajax files.
 *
 * @link       http://rextheme.com/
 * @since      1.0.0
 *
 * @package    Wpvr
 * @subpackage Wpvr/admin
 */

class Wpvr_Ajax {

  /**
   * Preview show ajax function
   */
  function wpvr_show_preview() {
      $panoid ='';
      $postid = sanitize_text_field($_POST['postid']);
      $post_type = get_post_type( $postid );

      $panoid = 'pano'.$postid;

      $control = sanitize_text_field($_POST['control']);
      if ($control == 'on') {
        $control = true;
      }
      else {
        $control = false;
      }

      $compass = sanitize_text_field($_POST['compass']);
      if ($compass == 'on') {
        $compass = true;
      }
      else {
        $compass = false;
      }

      $mouseZoom = sanitize_text_field($_POST['mouseZoom']);
      if ($mouseZoom == 'on') {
        $mouseZoom = true;
      }
      else {
        $mouseZoom = false;
      }

      $autoload = sanitize_text_field($_POST['autoload']);
      if ($autoload == 'on') {
        $autoload = true;
      }
      else {
        $autoload = false;
      }

      $default_scene = '';
      $default_scene = sanitize_text_field($_POST['defaultscene']);

      $preview = '';
      $preview = esc_url($_POST['preview']);

      $rotation = '';
      $rotation = sanitize_text_field($_POST['rotation']);

      $autorotation = '';
      $autorotation = sanitize_text_field($_POST['autorotation']);
      $autorotationinactivedelay = '';
      $autorotationinactivedelay = sanitize_text_field($_POST['autorotationinactivedelay']);
      $autorotationstopdelay = '';
      $autorotationstopdelay = sanitize_text_field($_POST['autorotationstopdelay']);

      if (!empty($autorotationinactivedelay) && !empty($autorotationstopdelay)) {
        wp_send_json_error('<p><span>Warning:</span> You can not apply both autorotation pause or stop delay. Please choose either autorotation inactive delay or autorotation stop delay</p>');
        die();
      }

      $scene_fade_duration = '';
      $scene_fade_duration = sanitize_text_field($_POST['scenefadeduration']);

      $panodata = $_POST['panodata'];
      $panolist = stripslashes($panodata);
      $panodata = (array)json_decode($panolist);
      $panolist = array();
      if(is_array($panodata["scene-list"])) {
        foreach ($panodata["scene-list"] as $scenes_data) {
          $temp_array = array();
          $temp_array = (array)$scenes_data;
          if ($temp_array['hotspot-list']) {
            $_hotspot_array = array();
            foreach ($temp_array['hotspot-list'] as $temp_hotspot) {
              $temp_hotspot = (array)$temp_hotspot;
              $_hotspot_array[] = $temp_hotspot;
            }
          }
          $temp_array['hotspot-list'] = $_hotspot_array;
          $panolist['scene-list'][] = $temp_array;
        }
      }
      $panodata = $panolist;

      //===Error Control and Validation===//

      if ($panodata["scene-list"] != "") {
        foreach ($panodata["scene-list"] as $scenes_val) {

          $scene_id_validate = $scenes_val["scene-id"];
          if (!empty($scene_id_validate)) {
            $scene_id_validated = preg_replace('/[^0-9a-zA-Z_]/',"",$scene_id_validate);
            if ($scene_id_validated != $scene_id_validate) {
              wp_send_json_error('<p><span>Warning:</span> The scene id can only contain letters and numbers where scene id: '.$scene_id_validate.'</p>');
              die();
            }
            if (empty($scenes_val["scene-attachment-url"])) {
              wp_send_json_error('<p><span>Warning:</span> A scene image is required for every scene where scene id: '.$scene_id_validate.'</p>');
              die();
            }

            if (!empty($scenes_val["scene-pitch"])) {
              $validate_scene_pitch = $scenes_val["scene-pitch"];
              $validated_scene_pitch = preg_replace('/[^0-9.-]/','',$validate_scene_pitch);
              if ($validated_scene_pitch != $validate_scene_pitch) {
                wp_send_json_error('<p><span>Warning:</span> Default pitch value can only contain float numbers where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if (!empty($scenes_val["scene-yaw"])) {
              $validate_scene_yaw = $scenes_val["scene-yaw"];
              $validated_scene_yaw = preg_replace('/[^0-9.-]/','',$validate_scene_yaw);
              if ($validated_scene_yaw != $validate_scene_yaw) {
                wp_send_json_error('<p><span>Warning:</span> Default yaw value can only contain float numbers where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if (!empty($scenes_val["scene-zoom"])) {
              $validate_default_zoom = $scenes_val["scene-zoom"];
              $validated_default_zoom = preg_replace('/[^0-9-]/','',$validate_default_zoom);
              if ($validated_default_zoom != $validate_default_zoom) {
                wp_send_json_error('<p><span>Warning:</span> Default zoom value can only contain number in degree from 50 to 120 where scene id: '.$scene_id_validate.'</p>');
                die();
              }
              $default_zoom_value = (int)$scenes_val["scene-zoom"];
              if ($default_zoom_value > 120 || $default_zoom_value < 50) {
                wp_send_json_error('<p><span>Warning:</span> Default zoom value can only contain number in degree from 50 to 120 where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if (!empty($scenes_val["scene-maxzoom"])) {
              $validate_max_zoom = $scenes_val["scene-maxzoom"];
              $validated_max_zoom = preg_replace('/[^0-9-]/','',$validate_max_zoom);
              if ($validated_max_zoom != $validate_max_zoom) {
                wp_send_json_error('<p><span>Warning:</span> Max zoom out value can only contain number in degree where scene id: '.$scene_id_validate.'</p>');
                die();
              }
              $max_zoom_value = (int)$scenes_val["scene-maxzoom"];
              if ($max_zoom_value > 120 ) {
                wp_send_json_error('<p><span>Warning:</span> Max zoom out value can only contain number in degree below 120 where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if (!empty($scenes_val["scene-minzoom"])) {
              $validate_min_zoom = $scenes_val["scene-minzoom"];
              $validated_min_zoom = preg_replace('/[^0-9-]/','',$validate_min_zoom);
              if ($validated_min_zoom != $validate_min_zoom) {
                wp_send_json_error('<p><span>Warning:</span> Max zoom in value can only contain number in degree where scene id: '.$scene_id_validate.'</p>');
                die();
              }
              $min_zoom_value = (int)$scenes_val["scene-minzoom"];
              if ($min_zoom_value < 50 ) {
                wp_send_json_error('<p><span>Warning:</span> Max zoom in value can only contain number in degree above 50 where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if ($scenes_val["hotspot-list"] != "") {
              foreach ($scenes_val["hotspot-list"] as $hotspot_val) {

                $hotspot_title_validate = $hotspot_val["hotspot-title"];

                if (!empty($hotspot_title_validate)) {
                  $hotspot_title_validated = preg_replace('/[^0-9a-zA-Z_]/',"",$hotspot_title_validate);
                  if ($hotspot_title_validated != $hotspot_title_validate) {
                    wp_send_json_error('<p><span>Warning:</span> Hotspot title can only contain letters and numbers where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                    die();
                  }

                  $hotspot_pitch_validate = $hotspot_val["hotspot-pitch"];
                  if (!empty($hotspot_pitch_validate)) {
                    $hotspot_pitch_validated = preg_replace('/[^0-9.-]/','',$hotspot_pitch_validate);
                    if ($hotspot_pitch_validated != $hotspot_pitch_validate) {
                      wp_send_json_error('<p><span>Warning:</span> Hotspot pitch can only contain float numbers where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }

                  $hotspot_yaw_validate = $hotspot_val["hotspot-yaw"];
                  if (!empty($hotspot_yaw_validate)) {
                    $hotspot_yaw_validated = preg_replace('/[^0-9.-]/','',$hotspot_yaw_validate);
                    if ($hotspot_yaw_validated != $hotspot_yaw_validate) {
                      wp_send_json_error('<p><span>Warning:</span> Hotspot yaw can only contain float numbers where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }

                  if(is_plugin_active( 'wpvr-pro/wpvr-pro.php' )){
                    $status  = get_option( 'wpvr_edd_license_status' );
                    if( $status !== false && $status == 'valid' ) {
                      if ($hotspot_val["hotspot-customclass-pro"] != 'none' && !empty($hotspot_val["hotspot-customclass"])) {
                        wp_send_json_error('<p><span>Warning:</span> Don\'t add Custom icon class and custom icon both where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                        die();
                      }
                    }
                  }
                  $hotspot_type_validate = $hotspot_val["hotspot-type"];
                  $hotspot_url_validate = $hotspot_val["hotspot-url"];
                  if (!empty($hotspot_url_validate)) {
                    $hotspot_url_validated = esc_url($hotspot_url_validate);
                    if ($hotspot_url_validated != $hotspot_url_validate) {
                      wp_send_json_error('<p><span>Warning:</span> Hotspot Url is invalid where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }
                  $hotspot_content_validate = $hotspot_val["hotspot-content"];

                  $hotspot_scene_validate = $hotspot_val["hotspot-scene"];

                  if ($hotspot_type_validate == "info") {
                    if (!empty($hotspot_scene_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Don\'t add Target Scene ID on info type hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                    if (!empty($hotspot_url_validate) && !empty($hotspot_content_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Don\'t add Url and On click content both on same hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }

                  if ($hotspot_type_validate == "scene") {
                    if (empty($hotspot_scene_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Target scene id is required for scene type hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                    if (!empty($hotspot_url_validate) || !empty($hotspot_content_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Don\'t add Url or On click content on scene type hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }
                }
              }
            }
          }
        }
      }
      //===Error Control and Validation===//
      foreach ($panodata["scene-list"] as $panoscenes) {
        if (empty($panoscenes['scene-id']) && !empty($panoscenes['scene-attachment-url'])) {
          wp_send_json_error('<p><span>Warning:</span> You have added a scene image but empty scene id. Please add scene id and update </p>');
          die();
        }
      }

      $allsceneids = array();

      foreach ($panodata["scene-list"] as $panoscenes) {
        if (!empty($panoscenes['scene-id'])) {
          array_push($allsceneids, $panoscenes['scene-id']);
        }
      }

      foreach ($panodata["scene-list"] as $panoscenes) {

        if ($panoscenes['dscene'] == 'on') {
          $default_scene = $panoscenes['scene-id'];
        }
      }
      if (empty($default_scene)) {
        if ($allsceneids) {
          $default_scene = $allsceneids[0];
        }
        else {
          wp_send_json_error('<p><span>Warning:</span> No default scene selected and no scene id found to set as default. You need at least one scene to publish a tour </p>');
          die();
        }
      }

      $allsceneids_count = array_count_values($allsceneids);
      foreach ($allsceneids_count as $key => $value) {
        if ($value > 1) {
          wp_send_json_error('<p><span>Warning:</span> You can not use same scene id on multiple scene </p>');
          die();
        }
      }

      foreach ($panodata["scene-list"] as $panoscenes) {
        if (!empty($panoscenes['scene-id'])) {
            $allhotspot = array();
            foreach ($panoscenes["hotspot-list"] as $hotspot_val) {
              if (!empty($hotspot_val["hotspot-title"])) {
                array_push($allhotspot, $hotspot_val["hotspot-title"]);
              }
            }
            $allhotspotcount = array_count_values($allhotspot);
            foreach ($allhotspotcount as $key => $value) {
              if ($value > 1) {
                wp_send_json_error('<p><span>Warning:</span> You can not use same hotspot id on multiple hotspot for same scene </p>');
                die();
              }
            }
        }
      }

      $default_data = array();
      $default_data = array("firstScene"=>$default_scene,"sceneFadeDuration"=>$scene_fade_duration);
      $scene_data = array();

      foreach ($panodata["scene-list"] as $panoscenes) {

        if (!empty($panoscenes['scene-id'])) {

          $scene_ititle = '';
          $scene_ititle = sanitize_text_field($panoscenes["scene-ititle"]);

          $scene_author = '';
          $scene_author = sanitize_text_field($panoscenes["scene-author"]);

          $scene_vaov = 180;
          $scene_vaov = (float)$panoscenes["scene-vaov"];

          $scene_haov = 360;
          $scene_haov = (float)$panoscenes["scene-haov"];

          $scene_vertical_offset = 0;
          $scene_vertical_offset = (float)$panoscenes["scene-vertical-offset"];

          $default_scene_pitch = null;
          $default_scene_pitch = (float)$panoscenes["scene-pitch"];

          $default_scene_yaw = null;
          $default_scene_yaw = (float)$panoscenes["scene-yaw"];

          $scene_max_pitch = '';
          $scene_max_pitch = (float)$panoscenes["scene-maxpitch"];

          $scene_min_pitch = '';
          $scene_min_pitch = (float)$panoscenes["scene-minpitch"];

          $scene_max_yaw = '';
          $scene_max_yaw = (float)$panoscenes["scene-maxyaw"];

          $scene_min_yaw = '';
          $scene_min_yaw = (float)$panoscenes["scene-minyaw"];

          $default_zoom = 100;
          $default_zoom = $panoscenes["scene-zoom"];
          if (!empty($default_zoom)) {
            $default_zoom = (int)$panoscenes["scene-zoom"];
          }
          else {
            $default_zoom = 100;
          }

          $max_zoom = 120;
          $max_zoom = $panoscenes["scene-maxzoom"];
          if (!empty($max_zoom)) {
            $max_zoom = (int)$panoscenes["scene-maxzoom"];
          }
          else {
            $max_zoom = 120;
          }

          $min_zoom = 50;
          $min_zoom = $panoscenes["scene-minzoom"];
          if (!empty($min_zoom)) {
            $min_zoom = (int)$panoscenes["scene-minzoom"];
          }
          else {
            $min_zoom = 50;
          }

          $hotspot_datas = $panoscenes["hotspot-list"];
          $hotspots = array();
          foreach ($hotspot_datas as $hotspot_data) {

            if (!empty($hotspot_data["hotspot-title"])) {
              $hotspot_info = array(
                "text"=>$hotspot_data["hotspot-title"],
                "pitch"=>$hotspot_data["hotspot-pitch"],
                "yaw"=>$hotspot_data["hotspot-yaw"],
                "type"=>$hotspot_data["hotspot-type"],
                "URL"=>$hotspot_data["hotspot-url"],
                "clickHandlerArgs"=>$hotspot_data["hotspot-content"],
                "createTooltipArgs"=>$hotspot_data["hotspot-hover"],
                "sceneId"=>$hotspot_data["hotspot-scene"],
                "targetPitch"=>(float)$hotspot_data["hotspot-scene-pitch"],
                "targetYaw"=>(float)$hotspot_data["hotspot-scene-yaw"]);
              array_push($hotspots, $hotspot_info);
              if (empty($hotspot_data["hotspot-scene"])) {
                unset($hotspot_info['targetPitch']);
                unset($hotspot_info['targetYaw']);
              }
            }
          }

          $scene_info = array();
          $scene_info = array("type"=>$panoscenes["scene-type"],"panorama"=>$panoscenes["scene-attachment-url"],"pitch"=>$default_scene_pitch,"maxPitch"=>$scene_max_pitch,"minPitch"=>$scene_min_pitch,"maxYaw"=>$scene_max_yaw,"minYaw"=>$scene_min_yaw,"yaw"=>$default_scene_yaw,"hfov"=>$default_zoom,"maxHfov"=>$max_zoom,"minHfov"=>$min_zoom,"title"=>$scene_ititle,"author"=>$scene_author ,"vaov"=>$scene_vaov, "haov"=>$scene_haov, "vOffset"=>$scene_vertical_offset, "hotSpots"=>$hotspots);

          if ($panoscenes["ptyscene"] == "off") {
            unset($scene_info['pitch']);
            unset($scene_info['yaw']);
          }

          if (empty($panoscenes["scene-ititle"])) {
            unset($scene_info['title']);
          }
          if (empty($panoscenes["scene-author"])) {
            unset($scene_info['author']);
          }

          if (empty($scene_vaov)) {
             unset($scene_info['vaov']);
          }

          if (empty($scene_haov)) {
             unset($scene_info['haov']);
          }

          if (empty($scene_vertical_offset)) {
             unset($scene_info['vOffset']);
          }

          if ($panoscenes["cvgscene"] == "off") {
             unset($scene_info['maxPitch']);
             unset($scene_info['minPitch']);
          }
          if (empty($panoscenes["scene-maxpitch"])) {
            unset($scene_info['maxPitch']);
          }

          if (empty($panoscenes["scene-minpitch"])) {
            unset($scene_info['minPitch']);
          }

          if ($panoscenes["chgscene"] == "off") {
            unset($scene_info['maxYaw']);
            unset($scene_info['minYaw']);

          }
          if (empty($panoscenes["scene-maxyaw"])) {
            unset($scene_info['maxYaw']);
          }

          if (empty($panoscenes["scene-minyaw"])) {
            unset($scene_info['minYaw']);
          }

          if ($panoscenes["czscene"] == "off") {
            unset($scene_info['hfov']);
            unset($scene_info['maxHfov']);
            unset($scene_info['minHfov']);
          }

          $scene_array = array();
          $scene_array = array(
            $panoscenes["scene-id"]=>$scene_info
          );
          $scene_data[$panoscenes["scene-id"]] = $scene_info;
          }

      }

      $pano_id_array = array();
      $pano_id_array = array("panoid"=>$panoid);
      $pano_response = array();
      $pano_response = array("autoLoad"=>$autoload,"showControls"=>$control,"compass"=>$compass,"mouseZoom"=>$mouseZoom,"preview"=>$preview,"autoRotate"=>$autorotation,"autoRotateInactivityDelay"=>$autorotationinactivedelay,"autoRotateStopDelay"=>$autorotationstopdelay,"default"=>$default_data,"scenes"=>$scene_data);

      if ($rotation == 'off') {
        unset($pano_response['autoRotate']);
        unset($pano_response['autoRotateInactivityDelay']);
        unset($pano_response['autoRotateStopDelay']);
      }
      if (empty($autorotation)) {
          unset($pano_response['autoRotate']);
          unset($pano_response['autoRotateInactivityDelay']);
          unset($pano_response['autoRotateStopDelay']);
      }
      if (empty($autorotationinactivedelay)) {
          unset($pano_response['autoRotateInactivityDelay']);
      }
      if (empty($autorotationstopdelay)) {
          unset($pano_response['autoRotateStopDelay']);
      }
      $response = array();
      $response = array($pano_id_array,$pano_response);

      wp_send_json_success( $response );
  }

  /**
  * Video Preview show ajax function
  */
  function wpvrvideo_preview() {
      $panoid ='';
      $postid = sanitize_text_field($_POST['postid']);
      $panoid = 'pano'.$postid;
      $randid = rand(1000, 1000000);
      $vidid = 'vid'.$randid;
      $videourl = sanitize_url($_POST['videourl']);
      $autoplay = sanitize_text_field($_POST['autoplay']);
      $loop = sanitize_text_field($_POST['loop']);

      $vidtype = '';
      if (strpos($videourl, 'youtube') > 0) {
        $vidtype = 'youtube';
        $explodeid = '';
        $explodeid = explode("=",$videourl);

        if ($autoplay == 'on') {
          $autoplay = '&autoplay=1';
        }
        else {
          $autoplay = '';
        }

        if ($loop == 'on') {
          $loop = '&loop=1';
        }
        else {
          $loop = '';
        }

        $foundid = '';
        $foundid = $explodeid[1].'?'.$autoplay.$loop;
        $html = '';
        $html .= '<iframe width="600" height="400" src="https://www.youtube.com/embed/'.$foundid.'" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

      } elseif (strpos($videourl, 'youtu.be') > 0) {
        $vidtype = 'youtube';
        $explodeid = '';
        $explodeid = explode("/",$videourl);

        if ($autoplay == 'on') {
          $autoplay = '&autoplay=1';
        }
        else {
          $autoplay = '';
        }

        if ($loop == 'on') {
          $loop = '&loop=1';
        }
        else {
          $loop = '';
        }

        $foundid = '';
        $foundid = $explodeid[3].'?'.$autoplay.$loop;
        $html = '';
        $html .= '<iframe width="600" height="400" src="https://www.youtube.com/embed/'.$foundid.'" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

      }
      elseif (strpos($videourl, 'vimeo') > 0) {
        $vidtype = 'vimeo';
        $explodeid = '';
        $explodeid = explode("/",$videourl);
        $foundid = '';

        if ($autoplay == 'on') {
          $autoplay = '&autoplay=1&muted=1';
        }
        else {
          $autoplay = '';
        }

        if ($loop == 'on') {
          $loop = '&loop=1';
        }
        else {
          $loop = '';
        }

        $foundid = $explodeid[3].'?'.$autoplay.$loop;
        $html = '';
        $html .= '<iframe src="https://player.vimeo.com/video/'.$foundid.'" width="600" height="400" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

      } else {
        $vidtype = 'selfhost';

        if ($autoplay == 'on') {
          $autoplay = 'autoplay';
        }
        else {
          $autoplay = '';
        }

        if ($loop == 'on') {
          $loop = 'loop';
        }
        else {
          $loop = '';
        }

        $html = '';
        $html .= '<video id="'.$vidid.'" class="video-js vjs-default-skin vjs-big-play-centered" '.$autoplay.' '.$loop.' controls preload="none" style="width:100%;height:400px;" poster="" >';
          $html .= '<source src="'.$videourl.'" type="video/mp4"/>';
          $html .= '<p class="vjs-no-js">';
            $html .= 'To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com html5-video-support/" target="_blank">supports HTML5 video</a>';
            $html .= '</p>';
        $html .= '</video>';
      }

      $response = array();
      $response = array(__( "panoid" )=>$panoid,__( "panodata" )=>$html,__( "vidid" )=>$vidid,__( "vidtype" )=>$vidtype);
      wp_send_json_success( $response );
  }

  function wpvr_save_data() {
      $panoid ='';
      $postid = sanitize_text_field($_POST['postid']);
      $post_type = get_post_type( $postid );
      if ($post_type != 'wpvr_item') {
        die();
      }
      $panoid = 'pano'.$postid;

      $streetview = $_POST['streetview'];
      if ($streetview == 'on') {
        $streetviewurl = sanitize_url($_POST['streetviewurl']);
        if ($streetviewurl) {
          $html .= '<iframe src="'.$streetviewurl.'" width="600" height="400" frameborder="0" style="border:0;" allowfullscreen=""></iframe>';
        }
        $streetviewarray = array();
        $streetviewarray = array(__( "panoid" )=>$panoid,__( "streetviewdata" )=>$html,__( "streetviewurl" )=>$streetviewurl,__( "streetview" )=>$streetview);
        update_post_meta( $postid, 'panodata', $streetviewarray );
        die();
      }

      $pnovideo = $_POST['panovideo'];
      if ($pnovideo == "on") {

        $vidid = 'vid'.$postid;
        $videourl = sanitize_url($_POST['videourl']);
        $autoplay = sanitize_text_field($_POST['autoplay']);
        $vidautoplay = sanitize_text_field($_POST['autoplay']);
        $loop = sanitize_text_field($_POST['loop']);
        $vidloop = sanitize_text_field($_POST['loop']);
        $vidtype = '';
        if (strpos($videourl, 'youtube') > 0) {
          $vidtype = 'youtube';
          $explodeid = '';
          $explodeid = explode("=",$videourl);
          $foundid = '';

          if ($autoplay == 'on') {
            $autoplay = '&autoplay=1';
          }
          else {
            $autoplay = '';
          }

          if ($loop == 'on') {
            $loop = '&loop=1';
          }
          else {
            $loop = '';
          }

          $foundid = $explodeid[1].'?'.$autoplay.$loop;
          $html = '';
          $html .= '<iframe width="600" height="400" src="https://www.youtube.com/embed/'.$foundid.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        } elseif (strpos($videourl, 'youtu.be') > 0) {
          $vidtype = 'youtube';
          $explodeid = '';
          $explodeid = explode("/",$videourl);
          $foundid = '';

          if ($autoplay == 'on') {
            $autoplay = '&autoplay=1';
          }
          else {
            $autoplay = '';
          }

          if ($loop == 'on') {
            $loop = '&loop=1';
          }
          else {
            $loop = '';
          }

          $foundid = $explodeid[3].'?'.$autoplay.$loop;
          $html = '';
          $html .= '<iframe width="600" height="400" src="https://www.youtube.com/embed/'.$foundid.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        }

        elseif (strpos($videourl, 'vimeo') > 0) {
          $vidtype = 'vimeo';
          $explodeid = '';
          $explodeid = explode("/",$videourl);
          $foundid = '';

          if ($autoplay == 'on') {
            $autoplay = '&autoplay=1&muted=1';
          }
          else {
            $autoplay = '';
          }

          if ($loop == 'on') {
            $loop = '&loop=1';
          }
          else {
            $loop = '';
          }

          $foundid = $explodeid[3].'?'.$autoplay.$loop;
          $html = '';
          $html .= '<iframe src="https://player.vimeo.com/video/'.$foundid.'" width="600" height="400" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

        } else {
          $vidtype = 'selfhost';
          $vidautoplay = '';
          $vidautoplay = sanitize_text_field($_POST['vidautoplay']);

          if ($autoplay == 'on') {
            $autoplay = 'autoplay muted';
          }
          else {
            $autoplay = '';
          }

          if ($loop == 'on') {
            $loop = 'loop';
          }
          else {
            $loop = '';
          }

          $html = '';
          $html .= '<video id="'.$vidid.'" class="video-js vjs-default-skin vjs-big-play-centered" '.$autoplay.' '.$loop.' controls preload="auto" style="width:100%;height:100%;" poster="" >';
            $html .= '<source src="'.$videourl.'" type="video/mp4"/>';
            $html .= '<p class="vjs-no-js">';
              $html .= 'To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com html5-video-support/" target="_blank">supports HTML5 video</a>';
              $html .= '</p>';
          $html .= '</video>';
        }



        $videoarray = array();
        $videoarray = array(__( "panoid" )=>$panoid,__( "panoviddata" )=>$html,__( "vidid" )=>$vidid,__( "vidurl" )=>$videourl,__( "autoplay" )=>$vidautoplay,__( "loop" )=>$vidloop,__( "vidtype" )=>$vidtype);
        update_post_meta( $postid, 'panodata', $videoarray );
        die();
      }


      $control = sanitize_text_field($_POST['control']);
      if ($control == 'on') {
        $control = true;
      }
      else {
        $control = false;
      }

      //===Custom Control===//
      $custom_control = $_POST['customcontrol'];

      //===Custom Control End===//

      $vrgallery = sanitize_text_field($_POST['vrgallery']);
      if ($vrgallery == 'on') {
        $vrgallery = true;
      }
      else {
        $vrgallery = false;
      }

      $gyro = sanitize_text_field($_POST['gyro']);

      if ($gyro == 'on') {
        $gyro = true;
      }
      else {
        $gyro = false;
      }

      $compass = sanitize_text_field($_POST['compass']);
      if ($compass == 'on') {
        $compass = true;
      }
      else {
        $compass = false;
      }

      $mouseZoom = sanitize_text_field($_POST['mouseZoom']);
      if ($mouseZoom == 'on') {
        $mouseZoom = true;
      }
      else {
        $mouseZoom = false;
      }

      $autoload = sanitize_text_field($_POST['autoload']);
      if ($autoload == 'on') {
        $autoload = true;
      }
      else {
        $autoload = false;
      }

      $default_scene = '';

      $preview = '';
      $preview = esc_url($_POST['preview']);

      $rotation = '';
      $rotation = sanitize_text_field($_POST['rotation']);

      $autorotation = '';
      $autorotation = sanitize_text_field($_POST['autorotation']);
      $autorotationinactivedelay = '';
      $autorotationinactivedelay = sanitize_text_field($_POST['autorotationinactivedelay']);
      $autorotationstopdelay = '';
      $autorotationstopdelay = sanitize_text_field($_POST['autorotationstopdelay']);

      if (!empty($autorotationinactivedelay) && !empty($autorotationstopdelay)) {
        wp_send_json_error('<p><span>Warning:</span> You can not apply both autorotation pause or stop delay. Please choose either autorotation inactive delay or autorotation stop delay</p>');
        die();
      }

      //===Company Logo===//
      $cpLogoSwitch = 'off';
      $cpLogoSwitch = $_POST['cpLogoSwitch'];
      $cpLogoImg = '';
      $cpLogoImg = $_POST['cpLogoImg'];
      $cpLogoContent = '';
      $cpLogoContent = sanitize_text_field($_POST['cpLogoContent']);
      //===Company Logo===//


      $scene_fade_duration = '';
      $scene_fade_duration = $_POST['scenefadeduration'];

      $panodata = $_POST['panodata'];
      $panolist = stripslashes($panodata);
      $panodata = (array)json_decode($panolist);
      $panolist = array();
      if(is_array($panodata["scene-list"])) {
        foreach ($panodata["scene-list"] as $scenes_data) {
          $temp_array = array();
          $temp_array = (array)$scenes_data;
          if ($temp_array['hotspot-list']) {
            $_hotspot_array = array();
            foreach ($temp_array['hotspot-list'] as $temp_hotspot) {
              $temp_hotspot = (array)$temp_hotspot;
              $_hotspot_array[] = $temp_hotspot;
            }
          }
          $temp_array['hotspot-list'] = $_hotspot_array;
          $panolist['scene-list'][] = $temp_array;
        }
      }
      $panodata = $panolist;

      //===Error Control and Validation===//

      if ($panodata["scene-list"] != "") {
        foreach ($panodata["scene-list"] as $scenes_val) {

          $scene_id_validate = $scenes_val["scene-id"];
          if (!empty($scene_id_validate)) {
            $scene_id_validated = preg_replace('/[^0-9a-zA-Z_]/',"",$scene_id_validate);
            if ($scene_id_validated != $scene_id_validate) {
              wp_send_json_error('<p><span>Warning:</span> The scene id can only contain letters and numbers where scene id: '.$scene_id_validate.'</p>');
              die();
            }

            if (empty($scenes_val["scene-attachment-url"])) {
              wp_send_json_error('<p><span>Warning:</span> A scene image is required for every scene where scene id: '.$scene_id_validate.'</p>');
              die();
            }

            if (!empty($scenes_val["scene-pitch"])) {
              $validate_scene_pitch = $scenes_val["scene-pitch"];
              $validated_scene_pitch = preg_replace('/[^0-9.-]/','',$validate_scene_pitch);
              if ($validated_scene_pitch != $validate_scene_pitch) {
                wp_send_json_error('<p><span>Warning:</span> Default pitch value can only contain float numbers where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if (!empty($scenes_val["scene-yaw"])) {
              $validate_scene_yaw = $scenes_val["scene-yaw"];
              $validated_scene_yaw = preg_replace('/[^0-9.-]/','',$validate_scene_yaw);
              if ($validated_scene_yaw != $validate_scene_yaw) {
                wp_send_json_error('<p><span>Warning:</span> Default yaw value can only contain float numbers where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if (!empty($scenes_val["scene-zoom"])) {
              $validate_default_zoom = $scenes_val["scene-zoom"];
              $validated_default_zoom = preg_replace('/[^0-9-]/','',$validate_default_zoom);
              if ($validated_default_zoom != $validate_default_zoom) {
                wp_send_json_error('<p><span>Warning:</span> Default zoom value can only contain number in degree from 50 to 120 where scene id: '.$scene_id_validate.'</p>');
                die();
              }
              $default_zoom_value = (int)$scenes_val["scene-zoom"];
              if ($default_zoom_value > 120 || $default_zoom_value < 50) {
                wp_send_json_error('<p><span>Warning:</span> Default zoom value can only contain number in degree from 50 to 120 where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if (!empty($scenes_val["scene-maxzoom"])) {
              $validate_max_zoom = $scenes_val["scene-maxzoom"];
              $validated_max_zoom = preg_replace('/[^0-9-]/','',$validate_max_zoom);
              if ($validated_max_zoom != $validate_max_zoom) {
                wp_send_json_error('<p><span>Warning:</span> Max zoom out value can only contain number in degree where scene id: '.$scene_id_validate.'</p>');
                die();
              }
              $max_zoom_value = (int)$scenes_val["scene-maxzoom"];
              if ($max_zoom_value > 120 ) {
                wp_send_json_error('<p><span>Warning:</span> Max zoom out value can only contain number in degree below 120 where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if (!empty($scenes_val["scene-minzoom"])) {
              $validate_min_zoom = $scenes_val["scene-minzoom"];
              $validated_min_zoom = preg_replace('/[^0-9-]/','',$validate_min_zoom);
              if ($validated_min_zoom != $validate_min_zoom) {
                wp_send_json_error('<p><span>Warning:</span> Max zoom in value can only contain number in degree where scene id: '.$scene_id_validate.'</p>');
                die();
              }
              $min_zoom_value = (int)$scenes_val["scene-minzoom"];
              if ($min_zoom_value < 50 ) {
                wp_send_json_error('<p><span>Warning:</span> Max zoom in value can only contain number in degree above 50 where scene id: '.$scene_id_validate.'</p>');
                die();
              }
            }

            if ($scenes_val["hotspot-list"] != "") {
              foreach ($scenes_val["hotspot-list"] as $hotspot_val) {

                $hotspot_title_validate = $hotspot_val["hotspot-title"];

                if (!empty($hotspot_title_validate)) {
                  $hotspot_title_validated = preg_replace('/[^0-9a-zA-Z_]/',"",$hotspot_title_validate);
                  if ($hotspot_title_validated != $hotspot_title_validate) {
                    wp_send_json_error('<p><span>Warning:</span> Hotspot title can only contain letters and numbers where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                    die();
                  }
                  $hotspot_pitch_validate = $hotspot_val["hotspot-pitch"];
                  if (empty($hotspot_pitch_validate)) {
                    wp_send_json_error('<p><span>Warning:</span> Hotspot pitch is required for every hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                    die();
                  }
                  if (!empty($hotspot_pitch_validate)) {
                    $hotspot_pitch_validated = preg_replace('/[^0-9.-]/','',$hotspot_pitch_validate);
                    if ($hotspot_pitch_validated != $hotspot_pitch_validate) {
                      wp_send_json_error('<p><span>Warning:</span> Hotspot pitch can only contain float numbers where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }

                  $hotspot_yaw_validate = $hotspot_val["hotspot-yaw"];
                  if (empty($hotspot_yaw_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Hotspot yaw is required for every hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                  }
                  if (!empty($hotspot_yaw_validate)) {
                    $hotspot_yaw_validated = preg_replace('/[^0-9.-]/','',$hotspot_yaw_validate);
                    if ($hotspot_yaw_validated != $hotspot_yaw_validate) {
                      wp_send_json_error('<p><span>Warning:</span> Hotspot yaw can only contain float numbers where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }

                  if(is_plugin_active( 'wpvr-pro/wpvr-pro.php' )){
                    $status  = get_option( 'wpvr_edd_license_status' );
                    if( $status !== false && $status == 'valid' ) {
                      if ($hotspot_val["hotspot-customclass-pro"] != 'none' && !empty($hotspot_val["hotspot-customclass"])) {
                        wp_send_json_error('<p><span>Warning:</span> Don\'t add Custom icon class and custom icon both where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                        die();
                      }
                    }
                  }
                  $hotspot_type_validate = $hotspot_val["hotspot-type"];
                  $hotspot_url_validate = $hotspot_val["hotspot-url"];
                  if (!empty($hotspot_url_validate)) {
                    $hotspot_url_validated = esc_url($hotspot_url_validate);
                    if ($hotspot_url_validated != $hotspot_url_validate) {
                      wp_send_json_error('<p><span>Warning:</span> Hotspot Url is invalid where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }
                  $hotspot_content_validate = $hotspot_val["hotspot-content"];

                  $hotspot_scene_validate = $hotspot_val["hotspot-scene"];

                  if ($hotspot_type_validate == "info") {
                    if (!empty($hotspot_scene_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Don\'t add Target Scene ID on info type hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                    if (!empty($hotspot_url_validate) && !empty($hotspot_content_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Don\'t add Url and On click content both on same hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }

                  if ($hotspot_type_validate == "scene") {
                    if (empty($hotspot_scene_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Target scene id is required for scene type hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                    if (!empty($hotspot_url_validate) || !empty($hotspot_content_validate)) {
                      wp_send_json_error('<p><span>Warning:</span> Don\'t add Url or On click content on scene type hotspot where scene id: '.$scene_id_validate.' and hotspot id : '.$hotspot_title_validate.'</p>');
                      die();
                    }
                  }
                }
              }
            }
          }
        }
      }
      //===Error Control and Validation===//

       foreach ($panodata["scene-list"] as $panoscenes) {
        if (empty($panoscenes['scene-id']) && !empty($panoscenes['scene-attachment-url'])) {
          wp_send_json_error('<p><span>Warning:</span> You have added a scene image but empty scene id. Please add scene id and update </p>');
          die();
        }
      }

      $allsceneids = array();

      foreach ($panodata["scene-list"] as $panoscenes) {
        if (!empty($panoscenes['scene-id'])) {
          array_push($allsceneids, $panoscenes['scene-id']);
        }
      }

      foreach ($panodata["scene-list"] as $panoscenes) {

        if ($panoscenes['dscene'] == 'on') {
          $default_scene = $panoscenes['scene-id'];
        }
      }
      if (empty($default_scene)) {
        if ($allsceneids) {
          $default_scene = $allsceneids[0];
        }
        else {
          wp_send_json_error('<p><span>Warning:</span> No default scene selected and no scene id found to set as default. You need at least one scene to publish a tour </p>');
          die();
        }
      }

      $allsceneids_count = array_count_values($allsceneids);
      foreach ($allsceneids_count as $key => $value) {
        if ($value > 1) {
          wp_send_json_error('<p><span>Warning:</span> You can not use same scene id on multiple scene </p>');
          die();
        }
      }

      foreach ($panodata["scene-list"] as $panoscenes) {
        if (!empty($panoscenes['scene-id'])) {
            $allhotspot = array();
            foreach ($panoscenes["hotspot-list"] as $hotspot_val) {
              if (!empty($hotspot_val["hotspot-title"])) {
                array_push($allhotspot, $hotspot_val["hotspot-title"]);
              }
            }
            $allhotspotcount = array_count_values($allhotspot);
            foreach ($allhotspotcount as $key => $value) {
              if ($value > 1) {
                wp_send_json_error('<p><span>Warning:</span> You can not use same hotspot id on multiple hotspot for same scene </p>');
                die();
              }
            }
        }
      }

      $panolength = count($panodata["scene-list"]);
      for ($i=0; $i < $panolength; $i++) {
        if (empty($panodata["scene-list"][$i]['scene-id'])) {
          unset($panodata["scene-list"][$i]);
        }
        else {
          $panohotspotlength = count($panodata["scene-list"][$i]['hotspot-list']);
          for ($j=0; $j < $panohotspotlength; $j++) {
            if (empty($panodata["scene-list"][$i]['hotspot-list'][$j]['hotspot-title'])) {
              unset($panodata["scene-list"][$i]['hotspot-list'][$j]);
            }
          }
        }
      }

      $pano_array = array();
      $pano_array = array(__( "panoid" )=>$panoid,__( "autoLoad" )=>$autoload,__( "showControls" )=>$control,__( "cpLogoSwitch" )=>$cpLogoSwitch,__( "cpLogoImg" )=>$cpLogoImg,__( "cpLogoContent" )=>$cpLogoContent,__( "vrgallery" )=>$vrgallery,__( "customcontrol" )=>$custom_control,__( "gyro" )=>$gyro,__( "compass" )=>$compass,__( "mouseZoom" )=>$mouseZoom,__( "autoRotate" )=>$autorotation,__( "autoRotateInactivityDelay" )=>$autorotationinactivedelay,__( "autoRotateStopDelay" )=>$autorotationstopdelay,__( "preview" )=>$preview,__( "defaultscene" )=>$default_scene,__( "scenefadeduration" )=>$scene_fade_duration,__( "panodata" )=>$panodata);

      if ($rotation == 'off') {
        unset($pano_array['autoRotate']);
        unset($pano_array['autoRotateInactivityDelay']);
        unset($pano_array['autoRotateStopDelay']);
      }
      if (empty($autorotation)) {
          unset($pano_array['autoRotate']);
          unset($pano_array['autoRotateInactivityDelay']);
          unset($pano_array['autoRotateStopDelay']);
      }
      if (empty($autorotationinactivedelay)) {
          unset($pano_array['autoRotateInactivityDelay']);
      }
      if (empty($autorotationstopdelay)) {
          unset($pano_array['autoRotateStopDelay']);
      }

      update_post_meta( $postid, 'panodata', $pano_array );
      die();
  }

  function wpvr_file_import() {
    set_time_limit(20000000000000000);
    wpvr_delete_temp_file();
    if ($_POST['fileurl']) {
      WP_Filesystem();
      $file_save_url = wp_upload_dir();
      $fileurl = $_POST['fileurl'];
      $attachment_id = $_POST['data_id'];
      $zip_file_path = get_attached_file( $attachment_id );
      $unzipfile = unzip_file($zip_file_path,$file_save_url['basedir'].'/wpvr/temp/');

      if ( is_wp_error( $unzipfile ) ) {
        wpvr_delete_temp_file();
        wp_send_json_error('Failed to unzip file');
      }
      $result = glob($file_save_url["basedir"].'/wpvr/temp/*.json');
      if (!$result) {
        wpvr_delete_temp_file();
        wp_send_json_error('Tour json file not found');
      }
      $tour_json = $result[0];
      $arrContextOptions=array(
        "ssl"=>array(
              "verify_peer"=>false,
              "verify_peer_name"=>false,
          ),
      );
      $getfile = file_get_contents($tour_json, false, stream_context_create($arrContextOptions));
      $file_content = json_decode($getfile, true);

      $new_title = $file_content['title'];
      $new_data = $file_content['data'];
      $new_post_id = wp_insert_post( array(
          'post_title'    => $new_title,
          'post_type'     => 'wpvr_item',
          'post_status'     => 'publish',
      ) );
      if ($new_post_id) {
        if ($new_data['panoid']) {
            $new_data['panoid'] = 'pano'.$new_post_id;
        }
        if ($new_data['preview']) {
          $preview_url = $file_save_url['baseurl'].'/wpvr/temp/scene_preview.jpg';
          $media_get = wpvr_handle_media_import($preview_url, $new_post_id);
          if ($media_get['status'] == 'error') {
              wp_delete_post($new_post_id, true);
              wpvr_delete_temp_file();
              wp_send_json_error($media_get['message']);
          }
          elseif ($media_get['status'] == 'success') {
            $new_data['preview'] = $media_get['message'];
          }
          else {
            wp_delete_post($new_post_id, true);
            wpvr_delete_temp_file();
            wp_send_json_error('Media transfer process failed');
          }
        }
        if ($new_data['panodata']) {

          if ($new_data['panodata']["scene-list"]) {

            foreach ($new_data['panodata']["scene-list"] as $key => $panoscenes) {
              if ($panoscenes["scene-attachment-url"]) {
                $scene_id = $panoscenes['scene-id'];
                $url = $file_save_url['baseurl'].'/wpvr/temp/'.$scene_id.'.jpg';
                $media_get = wpvr_handle_media_import($url, $new_post_id);
                if ($media_get['status'] == 'error') {
                    wp_delete_post($new_post_id, true);
                    wpvr_delete_temp_file();
                    wp_send_json_error($media_get['message']);
                }
                elseif ($media_get['status'] == 'success') {
                  $new_data['panodata']["scene-list"][$key]['scene-attachment-url'] = $media_get['message'];
                }
                else {
                  wp_delete_post($new_post_id, true);
                  wpvr_delete_temp_file();
                  wp_send_json_error('Media transfer process failed');
                }
              }
            }
          }
          update_post_meta( $new_post_id, 'panodata', $new_data );
          wpvr_delete_temp_file();
        }
      }
    }
    else {
      wpvr_delete_temp_file();
      wp_send_json_error('No file found to import');
    }
    die();
  }

  /**
  * Video Preview show ajax function
  */
  function wpvrstreetview_preview() {
      $panoid ='';
      $postid = sanitize_text_field($_POST['postid']);
      $panoid = 'pano'.$postid;
      $randid = rand(1000, 1000000);
      $streetviewid = 'streetview'.$randid;
      $streetviewurl = $_POST['streetview'];
      if ($streetviewurl) {
        $html .= '<iframe src="'.$streetviewurl.'" width="600" height="400" frameborder="0" style="border:0;" allowfullscreen=""></iframe>';
      }

      $response = array();
      $response = array(__( "panoid" )=>$panoid,__( "panodata" )=>$html,__( "streetview" )=>$streetviewid);
      wp_send_json_success( $response );
  }

  /**
  * Role management
  */
  function wpvr_role_management() {
    $editor = sanitize_text_field($_POST['editor']);
    $author = sanitize_text_field($_POST['author']);
    update_option('wpvr_editor_active', $editor);
    update_option('wpvr_author_active', $author);
    $response = array(
      'status' => 'success',
      'message' => 'Successfully saved',
    );
    wp_send_json($response);
  }

}
