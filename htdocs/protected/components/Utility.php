<?php

class Utility
{
	public static function timeAgo( $strtime )
	{
		$ago = time()-strtotime($strtime);
		if( $ago < 30 )
			return 'a few seconds ago';
		if( $ago < 120 )
			return 'a minute ago';
		if( $ago < 3600 )
			return (int)($ago/60) . ' minutes ago';
		if( $ago < 3600*2 )
			return 'an hour ago';
		if( $ago < 24*3600 )
			return (int)($ago/3600) . ' hours ago';
		if( $ago < 24*3600*2 )
			return 'a day ago';
		if( $ago < 365*24*3600 )
			return (int)($ago/(24*3600)) . ' days ago';
		return (int)($ago/(365*24*3600)) . ' years ago';
	}

	public static function clipStr( $str, $len=15 )
	{
		if( $len <= 0 )
			return $str;
		if( strlen($str) >= $len )
			return substr($str, 0, $len-3).'...';
		return $str;
	}

	private static $siSuffix = array('B', 'KB', 'MB', 'GB', 'TB', 'PB',
		'EB', 'ZB', 'YB');
	public static function bytesToStr( $bytes )
	{
		$depth = 0;
		while( $bytes >= 1000.0 )
		{
			$bytes /= 1000.0;
			$depth++;
		}

		return sprintf("%6.2f %s", $bytes, Utility::$siSuffix[$depth] );
	}

	public static function projectMenu( $bottom, $isTop )
	{
		$bc = array();
		$pj = $bottom->parent;
		while( $pj !== null )
		{
			$bc += array(
				$pj->name => array('project/view', 'id'=>$pj->projectid )
			);
			$pj = $pj->parent;
		}
		if( $isTop )
			$bc += array($bottom->name);
		else
			$bc += array( 
				$bottom->name => array('project/view', 'id'=>$bottom->projectid )
			);
		return $bc;
	}

	public static function genTeamTree( $idPrefix='', $selId=null )
	{
		Yii::app()->clientScript->registerCoreScript('jquery');
		Yii::app()->clientScript->registerCoreScript('jquery.ui');
		Yii::app()->clientScript->registerScriptFile( 
			"/js/jquery.jstree.js",
			CClientScript::POS_HEAD
		);

		$openId = null;
		echo '<ul>';
		foreach( Yii::app()->user->getModel()->teams as $team )
		{
			$id = $idPrefix.'project-tree-t'.$team->teamid;
			if( $selId == $id )
				echo '<li class="active-leaf" ';
			else
				echo '<li ';
			echo 'rel="team" id="'.$id.'"><a href="'.
				Yii::app()->createUrl('/team/view', array('id'=>$team->teamid)).'">'.
				$team->name . '</a><ul>';
			$id = Utility::genProjectTree( $team->teamid, NULL,
				$idPrefix, $selId );
			if( $openId === null )
				$openId = $id;
			echo '</ul></li>';
		}
		echo '</ul>';

		return $openId;
	}

	private static function genProjectTree( $teamid, $parentid, $idPrefix,
		$selId )
	{
		$projects = Project::model()->findAllByAttributes(array(
			'parentid' => $parentid,
			'teamid' => $teamid,
		));
		$openId = null;
		foreach( $projects as $project )
		{
			$id = $idPrefix.'project-tree-p'.$project->projectid;
			if( $selId == $id )
			{
				if( $project->parentid === NULL )
					$openId = $idPrefix.'project-tree-t'.$project->teamid;
				else
					$openId = $idPrefix.'project-tree-p'.$project->parentid;
				echo '<li class="active-leaf" ';
			}
			else
				echo '<li ';
			echo 'id="'.$id.'"><a href="'.
				Yii::app()->createUrl('/project/view', array(
				'id'=>$project->projectid)).'">' . $project->name . '</a><ul>';
			$id = Utility::genProjectTree( $teamid, $project->projectid,
				$idPrefix, $selId );
			if( $openId === null )
				$openId = $id;
			echo '</ul></li>';
		}

		return $openId;
	}

	public static function avatarImg( $userid, $size='miniIcon' )
	{
		return '<img src="'.Yii::app()->createUrl('file/index', array(
			'cat'=>'avatar', 'size'=>$size, 'id'=>$userid)).'">';
	}

	public static function userList( $users, $icons=true )
	{
		$youthere = false;
		$out = array();
		foreach( $users as $asg )
		{
			if( $asg->userid == Yii::app()->user->id )
				$youthere = true;
			else
				$out[] = $asg->dispName();
		}
		if( $youthere )
			array_unshift( $out, Yii::app()->user->getModel()->dispName() );
		return implode( $out, ', ');
	}
}

