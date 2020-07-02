<?php

class AUTOAVATARS_CLASS_EventHandler
{	
	
	/*
	 * Sets a sex-specific user profile pic when a new user joins
	 * Does not run if the user uploaded a profile pic when joining
	 * 
	 * @param array OW_Event
	 * 
	 */
	public function setProfilePic( OW_Event $event ) 
	{
		$params = $event->getParams();
		$userId = $params['userId'];

		$doesAvatarExist = BOL_AvatarService::getInstance()->findByUserId( $userId );

		if ( !$doesAvatarExist ) 
		{
			$username = BOL_UserService::getInstance()->getUserName ( $userId );

			$data = BOL_QuestionService::getInstance()->getQuestionData(array( $userId ), array('sex'));
			$sex = $data[ $userId ]['sex'];

			$avatarMatchingUserSex = BOL_ConfigService::getInstance()->findConfigValue('autoavatars', 'gender-' . $sex );

			$imagesUrl = OW::getPluginManager()->getPlugin('autoavatars')->getUserFilesUrl();
			$imagePath = $imagesUrl . $avatarMatchingUserSex;

			BOL_AvatarService::getInstance()->setUserAvatar (
				$userId, 
				$imagePath, 
				array('trackAction' => FALSE) );
		}
	}
    
    // Bind events
    public function init ()
    {
		OW::getEventManager()->bind('base.user_register', array($this, 'setProfilePic'));
    }
}
