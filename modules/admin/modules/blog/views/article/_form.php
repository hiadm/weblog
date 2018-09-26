<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AdminLteICheckAsset;
use app\assets\EditormdAsset;


EditormdAsset::register($this);
AdminLteICheckAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\blog\Article */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'category_id')->dropDownList($category, ['prompt' => '选择所属分类','id'=>'category_btn'])?>

        <?= $form->field($model, 'brief')->textarea(['maxlength' => true]) ?>

        <?php $form->field($model, 'image')->fileInput(['maxlength' => true]) ?>


        <?= $form->field($model, 'type')->radioList([
                '0' => '原创',
                '1' => '转载',
                '2' => '翻译'
        ]) ?>

        <div class="form-group field-article-content">
            <label class="control-label" for="article-content">文章内容</label>
            <?= $form->field($model, 'content_str',['options'=>['id'=>'editormd']])->textarea() ?>
            <div class="help-block"></div>
        </div>

        <?= $form->field($model, 'tag_arr', ['options'=>['id'=>'tags_container']])->checkboxList($tags)->hint('选择分类，展示可用标签。')?>

        <?= $form->field($model, 'tag_str')->textInput()->hint('多个标签可用逗号( , )分隔。')?>

        <?= $form->field($model, 'draft')->hiddenInput(['id'=>'draft_input'])->label(false)?>

    </div>
    <div class="box-footer">
        <?= Html::button('立即发布', ['class' => 'btn btn-success btn-flat', 'id'=>'publish_btn']) ?>
        <?= Html::button('保存至草稿箱', ['class' => 'btn btn-warning btn-flat', 'id'=>'draft_btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php $this->registerJs('initICheck();')?>

<?php
$js = <<<SCRIPT
    var editor = editormd("editormd", {
        path : "/static/libs/editormd/lib/",
        width   : "100%",
        height  : 640,
        syncScrolling : "single"

        
    });

    //点击发布
    $('#draft_btn').on('click', function(){
        $('#draft_input').val(1).closest('form').submit();
    });
    $('#publish_btn').on('click', function(){
        $('#draft_input').val(0).closest('form').submit();
    });
SCRIPT;

$this->registerJs($js);
?>

<?php
//展示标签
$getTagsUrl = Url::to(['tag/get-tags-by-category']);
$article_id = (int) $model->id;
$showTag = <<<TAG

    $('#category_btn').on('change', function(){
        var that = $(this);
        var value = that.val();
        
        if( !( parseInt( value ) > 0 ) ) return;
        
        $.ajax({
            url : "$getTagsUrl",
            type : 'POST',
            data : {category_id : value, article_id : $article_id},
            success : function(d){
                var oInput = '';
                if( d.errno === 0 ){
                    $.each( d.data, function(i, n){
                        if( d.hasTags.indexOf( i ) >= 0){
                            oInput += '<label><input type="checkbox" checked="checked" name="Article[tag_arr][]" value="'+ i +'"> '+ n +' </label> ';
                        }else{
                            oInput += '<label><input type="checkbox" name="Article[tag_arr][]" value="'+ i +'"> '+ n +' </label> ';
                        }
                    });
                }else{
                    oInput = d.errmsg;
                }
                $('#tags_container > #article-tag_arr').html( oInput );
                
                //刷新ichck插件
                initICheck();
                
            }
        });
        
    });

TAG;
$this->registerJs($showTag);
?>

<script>
    function initICheck(){
        //icheck插件
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    }
</script>
