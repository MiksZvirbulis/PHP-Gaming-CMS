<?php
class forum{
	# Starting functions

	public static function returnStatistics($id, $type){
		global $db;
		$total = 0;
		if($type == "fposts"){
			$topics = $db->fetchAll("SELECT `tid` FROM `topics` WHERE `forum_id` = ?", array($id));
			foreach($topics as $topic){
				$total += $db->count("SELECT `pid` FROM `posts` WHERE `topic_id` = ?", array($topic['tid']));
			}

			$subforums = $db->fetchAll("SELECT `id` FROM `forums` WHERE `parent_id` = ?", array($id));
			foreach($subforums as $forum){
				$subtopics = $db->fetchAll("SELECT `tid` FROM `topics` WHERE `forum_id` = ?", array($forum['id']));
				foreach($subtopics as $topic){
					$total += $db->count("SELECT `pid` FROM `posts` WHERE `topic_id` = ?", array($topic['tid']));
				}
			}
		}elseif($type == "topics"){
			$total = $db->count("SELECT `tid` FROM `topics` WHERE `forum_id` = ?", array($id));
			$subforums = $db->fetchAll("SELECT `id` FROM `forums` WHERE `parent_id` = ?", array($id));
			foreach($subforums as $forum){
				$total += $db->count("SELECT `tid` FROM `topics` WHERE `forum_id` = ?", array($forum['id']));
			}
		}elseif($type == "posts"){
			$total = $db->count("SELECT `pid` FROM `posts` WHERE `topic_id` = ?", array($id));
		}elseif($type == "views"){
			$total = $db->count("SELECT `id` FROM `views` WHERE `parent_type` = ? AND `parent_id` = ?", array("topic", $id));
		}
		return $total;
	}

	public static function updateViews($parent_type, $parent_id){
		global $db;
		$find = $db->count("SELECT `id` FROM `views` WHERE `parent_type` = ? AND `parent_id` = ? AND `ip_address` = ?", array(
			$parent_type,
			$parent_id,
			user::getIP()
			));
		if($find == 0){
			$db->insert("INSERT INTO `views` (`parent_type`, `parent_id`, `ip_address`, `user_id`, `time`) VALUES (?, ?, ?, ?, ?)", array(
				$parent_type,
				$parent_id,
				user::getIP(),
				(user::isLoggedIn() === true) ? $_SESSION['user_id'] : 0,
				time()
				));
		}
	}
}