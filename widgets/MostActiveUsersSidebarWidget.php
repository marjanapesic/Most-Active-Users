<?php
class MostActiveUsersSidebarWidget extends HWidget {
	
	public function run() {

		$assetPrefix = Yii::app()->assetManager->publish(dirname(__FILE__) . '/../resources', true, 0, defined('YII_DEBUG'));
        Yii::app()->clientScript->registerCssFile($assetPrefix . '/mostactiveusers.css');

		$noUsers = HSetting::Get('noUsers', 'mostactiveusers');
		$noUsers = $noUsers == '' || $noUsers == null ? 0 : $noUsers;
		$users = $this->getMostActiveUsers ( $noUsers );
		if(!empty($users)) {
			$this->render ( 'mostActiveUsersPanel', array (
			'users' => $users
			) );
		}
	}
	
	private function getMostActiveUsers ($range = 5) {
		
		$users = array ();

		//query that selects $range number of most active users. Selects profile information, count of posts, comments and likes
      	//ordered by number of posts,comments and likes
		$query = "SELECT profile.*, coalesce (post.cnt, 0) as posts, coalesce(comment.cnt, 0) as comments, coalesce(l.cnt, 0) as likes FROM profile
			LEFT JOIN (SELECT created_by, count(*) as cnt FROM post GROUP BY created_by) post
			ON post.created_by = profile.user_id
			LEFT JOIN (SELECT created_by, count(*) as cnt FROM comment GROUP BY created_by) comment
			ON comment.created_by = profile.user_id
			LEFT JOIN (SELECT created_by, count(*) as cnt FROM `like` GROUP BY created_by) l
			ON l.created_by = profile.user_id
			ORDER BY post.cnt DESC, comment.cnt DESC, l.cnt DESC
			LIMIT ".$range;

		$users = Yii::app()->db->createCommand($query)->queryAll();

		return $users;    
	}
}

?>
