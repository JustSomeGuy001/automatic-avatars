<?php

class AUTOAVATARS_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    /*
     * Handles display and form submission for Admin Settings page
     * 
     */
    public function settings()
    {
        $language = OW::getLanguage();

        OW::getDocument()->setTitle($language->text("autoavatars", "admin_page_title"));
        OW::getDocument()->setHeading($language->text("autoavatars", "admin_page_heading"));

        // Create image upload form
        $fileStorageForm = new Form('addNewAvatarForm');
        $fileStorageForm->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

        // Field for entering image
        $image = new FileField('image');
        $image->setLabel($language->text('autoavatars', 'upload_image'));
        $fileStorageForm->addElement($image);

        // Field for entering sex
        $sex = new Selectbox('newAssignGender');
        $sex->setLabel($language->text('autoavatars', 'select_sex'));
        $values = BOL_QuestionService::getInstance()->findQuestionsValuesByQuestionNameList(array('sex'));
        $valuesArray = array();
        $gendersArray = array();
        foreach ( $values['sex']['values'] as $value )
        {
            $valuesArray[$value->value] = OW::getLanguage()->text( 'base', 'questions_question_' . $value->questionName . '_value_' . ($value->value) );
            $gendersArray[] = array(
                "sex" => OW::getLanguage()->text( 'base', 'questions_question_' . $value->questionName . '_value_' . ($value->value) ),
                "displayUrl" => $this->checkForUrl($value->value),
                "deleteUrl" => OW::getRouter()->urlForRoute('autoavatars.admin_config-remove-item', array('sex' => $value->value))
            );
        }
        $sex->setOptions($valuesArray);
        $sex->setRequired();
        $fileStorageForm->addElement($sex);

        $this->assign("assignedAvatars", $gendersArray);

        $submitButton = new Submit('submit');
        $submitButton->setValue($language->text('autoavatars', 'submit_image'));
        $fileStorageForm->addElement($submitButton);

        $this->addForm($fileStorageForm);
        $showForm = true;

        // Handle Form Submission
        if ( OW::getRequest()->isPost() && $fileStorageForm->isValid($_POST) )
        {
            if ( (int) $_FILES['image']['error'] !== 0 || !is_uploaded_file($_FILES['image']['tmp_name']) || !UTIL_File::validateImage($_FILES['image']['name']) )
            {
                $imageValid = false;
                OW::getFeedback()->error($language->text('base', 'not_valid_image'));
            }
            else
            {
                $imageValid = true;
            }

            $data = $fileStorageForm->getValues();

            if ( $imageValid )
            {
                $showForm = false;
                $chosenGender = $data['newAssignGender'];
                $chosenGenderName = OW::getLanguage()->text( 'base', 'questions_question_' . $value->questionName . '_value_' . $chosenGender );
                $storage = OW::getStorage();

                // Get file extension type
                $path = $_FILES['image']['name'];
                $pathInfo = pathinfo($path);
                $ext = $pathInfo['extension'];

                $imageName = 'autoavatars_gender_' . $chosenGender . '.' . $ext;
                $imagePath = $this->getImagesDir() . $imageName;

                if ( $storage->fileExists($imagePath) )
                {
                    $storage->removeFile($imagePath);
                }

                $pluginfilesDir = Ow::getPluginManager()->getPlugin('autoavatars')->getPluginFilesDir();
                $tmpImgPath = $pluginfilesDir . 'file_storage_' .uniqid() . '.' . $ext;

                $image = new UTIL_Image($_FILES['image']['tmp_name']);
                $image->resizeImage(500, null)->saveImage($tmpImgPath);

                unlink($_FILES['image']['tmp_name']);

                $storage->copyFile($tmpImgPath, $imagePath);

                unlink($tmpImgPath);

                $this->assign('imageUrl', $this->getImagesUrl() . $imageName);

                // Add or Update config values
                $doesConfigExistForSelectedSex = BOL_ConfigService::getInstance()->findConfig('autoavatars', 'gender-' . $chosenGender );
                if (!$doesConfigExistForSelectedSex) 
                {
                    BOL_ConfigService::getInstance()->addConfig(
                        'autoavatars', 
                        'gender-' . $chosenGender, 
                        $imageName, 
                        'Filename for ' . $chosenGenderName . ' sex'
                    );
                } 
                else 
                {
                    BOL_ConfigService::getInstance()->saveConfig('autoavatars', 'gender-' . $chosenGender, $imageName);
                }

                $this->redirect();
            }
        }
        $this->assign('showForm', $showForm);

    }

    /*
     * Checks whether avatar file exists on disk
     * 
     * If found, returns URL for image
     * Else, returns NULL
     * 
     * @param integer $sex
     * @return string
     * 
     */
    private function checkForUrl($sex)
    { 

        $imageName = BOL_ConfigService::getInstance()->findConfigValue('autoavatars', 'gender-' . $sex );

        if (!$imageName) 
        {
            return null;
        }

        $imagePath = $this->getImagesDir() . $imageName;

        // Added timestamp to avoid caching
        $urlForImage = $this->getImagesUrl() . $imageName . '?' . time();

        return OW::getStorage()->fileExists($imagePath) ? $urlForImage : null; 
    }
 
    /*
     * Handles avatar removal
     * 
     * Calls removeFile() and removes config 
     * 
     * @param array
     * 
     */
    public function delete( $params )
    {
        $sex = $params['sex'];

        if ( isset($sex) )
        {
            $this->removeFile($sex);

            BOL_ConfigService::getInstance()->removeConfig('autoavatars', 'gender-' . $sex);
        }

        $this->redirect(OW::getRouter()->urlForRoute('autoavatars.admin'));
    }

    /*
     * Deletes avatar file from disk
     * 
     * @param integer $sex
     * 
     */
    private function removeFile ($sex)
    {
        $imageName = 'autoavatars_gender_' . $sex . '.jpg';
        $imagePath = $this->getImagesDir() . $imageName;

        if ( OW::getStorage()->fileExists($imagePath) )
        {
            OW::getStorage()->removeFile($imagePath);
        }
    }
    
    /*
     * Gets directory where avatar images are stored
     * 
     * @return string
     * 
     */
    private function getImagesDir() 
    {
        return OW::getPluginManager()->getPlugin('autoavatars')->getUserFilesDir();
    }

    /*
     * Gets URL for directory containing avatar images
     * 
     * @return string
     * 
     */
    private function getImagesUrl() 
    {
        return OW::getPluginManager()->getPlugin('autoavatars')->getUserFilesUrl();
    }
}