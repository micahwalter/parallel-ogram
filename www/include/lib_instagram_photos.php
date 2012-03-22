<?php

	loadlib("instagram_photos_lookup");

	#################################################################

	function instagram_photos_get_by_id($photo_id){

		$lookup = instagram_photos_lookup_get_by_photo_id($photo_id);

		if (! $lookup){
			return null;
		}

		$user = users_get_by_id($lookup['user_id']);
		$cluster_id = $user['cluster_id'];

		$enc_photo = AddSlashes($photo_id);

		$sql = "SELECT * FROM InstagramPhotos WHERE id='{$enc_photo}'";
		return db_single(db_fetch_users($cluster_id, $sql));
	}

	#################################################################

	function instagram_photos_add_photo($photo){

		$user = users_get_by_id($photo['user_id']);
		$cluster_id = $user['cluster_id'];

		$insert = array();

		foreach ($photo as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert_users($cluster_id, 'InstagramPhotos', $insert);

		if (! $rsp['ok']){
			return $rsp;
		}

		$rsp_lookup = instagram_photos_lookup_add_photo($photo['id'], $user['id']);

		if (! $rsp_lookup){
			return not_okay("photo row added; lookup failed: {$rsp_lookup['error']}");
		}

		$rsp['photo'] = $photo;
		return $rsp;
	}

	#################################################################

	function instagram_photos_update_photo($photo, $update){

		$user = users_get_by_id($photo['user_id']);
		$cluster_id = $user['cluster_id'];

		$insert = array();

		foreach ($update as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$enc_photo = AddSlashes($photo['id']);
		$where = "id='{$enc_photo}'";

		return db_update_users($cluster_id, 'InstagramPhotos', $insert, $where);
	}

	#################################################################
?>