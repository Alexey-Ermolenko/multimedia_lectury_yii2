<?
$this->registerJsFile('/modules/admin/web/js/admin_edit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="container-fluid">
    <?
    if (Yii::$app->user->identity->role == \app\models\User::ROLE_ADMIN)
    {
        echo '<h1>Данный пользователь - Администратор</h1>';
    } else {
        echo '<h1>Пользователь</h1>';
    }
	
	// Yii::$app->userHelperClass->pre(Yii::$app->user);

    // http://lectory.yii/admin/page/user/?id=10
    // Yii::$app->userHelperClass->pre('user');
    // Yii::$app->userHelperClass->pre(Yii::$app->user);
    ?>
    <div class="card">
        <div class="card-header indigo   lighten-1 white-text">
            Форма редактирования пользователя
        </div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <!--<input type="hidden" name="user[_csrf]" value="<?=Yii::$app->request->getCsrfToken()?>" />-->
                <input type="hidden" name="user[id]" value="<?=Yii::$app->user->identity->getId()?>" />
                <div class="md-form">
                    <i class="fa fa-user prefix grey-text"></i>Имя:
                    <input type="text" id="form3" name="user[username]" class="form-control" value="<?=Yii::$app->user->identity->username?>">
                </div>
                <div class="md-form">
                    <i class="fa fa-envelope prefix grey-text"></i>E-mail:
                    <input type="text" id="form2" name="user[email]" class="form-control" value="<?=Yii::$app->user->identity->email?>">
                </div>
                <div class="md-form">
                    <i class="fa fa-pencil prefix grey-text"></i>Описание:
                    <textarea type="text" id="form8" name="user[desc]" class="md-textarea"><?=Yii::$app->user->identity->desc?></textarea>
                </div>
                <div class="md-form">
                    <img src="<?='http://'.$_SERVER['HTTP_HOST'].'/'.Yii::$app->user->identity->img_src?>" alt="<?Yii::$app->user->identity->img_src?>" class="edit_form-img float-left img-thumbnail">
                </div>
                <br>
                <div class="md-form file-field">
                    <div class="btn btn-primary btn-sm">
                        <span>Выбрать файл</span>
                        <input type="file" value="<?=Yii::$app->user->identity->img_src?>" name="img_src">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" value="admin.jpg" type="text" placeholder="Загрузите изображение пользователя">
                    </div>
                </div>
                <div class="text-left">
                    <button type="submit" class="btn btn-success">Отправить <i class="fa fa-paper-plane-o ml-1"></i></button>
                </div>
            </form>


        </div>
    </div>
</div>


