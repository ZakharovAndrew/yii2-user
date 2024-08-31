<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\user\assets\UserAssets;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\user\models\User $model */
?>
<style>
#dropzone {
    border: 2px dashed #aaa;
    padding: 45px 0 19px;
    text-align: center;
    cursor: pointer;
    margin: 10px 0; 
    transition: background-color 0.3s;
    border-radius: 8px;
    border: 2px dashed rgba(17, 17, 17, 0.2);
}

#dropzone.active, #dropzone:hover{
    border-color: #008CBA;
    background-color: rgba(17, 17, 17, 0.05);
}
.profile-picture {
    width: 160px;
    height: 160px;
    background-size: cover;
    margin:0 auto;
    margin-bottom:20px;
    background-image: url('<?= Yii::$app->assetManager->getAssetUrl(UserAssets::register($this), 'images/default-avatar.png') ?>')
}
#user-avatar {
    display:none;
}
.simulate_link {
    display: inline-block;
    color: rgb(25, 123, 255);
}
.user-upload-avatar .form-group {
    text-align:center;
}
.user-upload-avatar button {
    margin: 25px 0 0;
}
</style>
<div class="user-upload-avatar">
    <h1>Upload Avatar</h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="white-block">
        <p>Select an image file to upload</p>
        <div id="dropzone">
            <div class="profile-picture" style="width:160px; height:160px"></div>
            <div id="message"><div class="simulate_link">Choose file</div> or drop here</div>
        <?= $form->field($model, 'avatar')->fileInput()->label(false) ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Upload Image', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('user-avatar');

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('active');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('active');
    });

    dropzone.addEventListener('drop', (e) => {
        console.log('asd');
        e.preventDefault();
        dropzone.classList.remove('active');
        fileInput.files = e.target.files;        
        
        fileInput.files = e.dataTransfer.files;
        uploadPreview();
    });
    
    dropzone.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
        uploadPreview();
    });
    
    function uploadPreview() {
        const fileUploadInput = document.querySelector('#user-avatar');

        /// Validations ///

        if (!fileUploadInput.value) {
          return;
        }

        // using index [0] to take the first file from the array
        const image = fileUploadInput.files[0];

        // check if the file selected is not an image file
        if (!image.type.includes('image')) {
          return alert('Only images are allowed!');
        }

        // check if size (in bytes) exceeds 10 MB
        if (image.size > 10_000_000) {
          return alert('Maximum upload size is 10MB!');
        }

        /// Display the image on the screen ///

        const fileReader = new FileReader();
        fileReader.readAsDataURL(image);

        fileReader.onload = (fileReaderEvent) => {
          const profilePicture = document.querySelector('.profile-picture');
          profilePicture.style.backgroundImage = `url(${fileReaderEvent.target.result})`;
        }
    }
</script>