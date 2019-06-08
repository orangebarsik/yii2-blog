<?php

namespace orangebarsik\blog\models;

use common\components\behaviors\StatusBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\image\drivers\Image;


/**
 * This is the model class for table "blog".
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property string $image
 * @property string $url
 * @property int $status_id
 * @property int $sort
 * @property string $date_create
 * @property string $date_update
 */
class Blog extends ActiveRecord
{

    const STATUS_LIST = ['off', 'on'];
    public $new_tags;
    public $file;

    /* public $tags_array; */

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog';
    }

    public function behaviors()
    {
        return [
           'timestampBehavior:' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => 'date_update',
                'value' => new Expression('NOW()'),
            ],
            'statusBehavior' => [
                'class' => StatusBehavior::className(),
                'statusList' => self::STATUS_LIST,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
			[['title', 'url'], 'required'],
            [['text'], 'string'],
			[['url'], 'unique'],
            [['status_id', 'sort'], 'integer'],
			[['sort'], 'integer', 'min' => 1, 'max' => 99],
            [['title', 'url'], 'string', 'max' => 150],
            [['image'], 'string', 'max' => 100],
            [['file'], 'image'],
            [['new_tags', 'date_create', 'date_update'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'url' => 'ЧПУ',
            'status_id' => 'Статус',
            'sort' => 'Сортировка',
            'tagsAsString' => 'Теги',
            'new_tags' => 'Теги',
            'image' => 'Картинка',
            'file' => 'Картинка',
            'author.username' => 'Имя Автора',
            'author.email' => 'Почта Автора',
            'date_create' => 'Создано',
            'date_update' => 'Обновлено',
        ];
    }

	public function getAuthor(){
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }

    public function getImages() {
        return $this->hasMany(\common\models\ImageManager::className(), ['item_id' => 'id'])->andWhere(['class' => self::tableName()])->orderBy('sort');
    }

    public function getImagesLinks()
    {
        return ArrayHelper::getColumn($this->images, 'imageUrl');
    }

    public function getImagesLinksData()
    {
        $arr = ArrayHelper::toArray($this->images, [
                \common\models\ImageManager::className() => [
                    'caption' => 'name',
                    'key' => 'id',
                ]
            ]
        );
        return $arr;
    }

    public function getBlogTag(){
        return $this->hasMany(BlogTag::className(), ['blog_id' => 'id']);
    }

    public function getTags(){
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->via('blogTag');
    }

    public function getTagsAsString()
    {
        $arr = ArrayHelper::map($this->tags, 'id', 'name');
        return implode(', ', $arr);
    }

    public function getSmallImage()
    {
        if($this->image){
            $path = str_replace('admin.', '', Url::home(true)) . 'uploads/images/blog/' . '50x50' . '/' . $this->image;
        } else {
            $path = str_replace('admin.', '', Url::home(true)) . 'uploads/images/nophoto.jpg';
        }
        return $path;
    }


    public function beforeDelete()
    {
        if(parent::beforeDelete()){
            BlogTag::deleteAll(['blog_id' => $this->id]);
            return true;
        } else {
            return false;
        }
    }

    /*
    public function afterFind()
    {
        $this->tags_array = $this->tags;
    }
    */

    public function afterFind()
    {
        parent::afterFind();
        $this->new_tags =  ArrayHelper::map($this->tags, 'name', 'name') ;
    }


    /*
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $arr = \yii\helpers\ArrayHelper::map($this->tags, 'id', 'id');
        foreach ($this->tags_array as $one){
            if(!in_array($one, $arr)){
                $model = new BlogTag();
                $model->blog_id = $this->id;
                $model->tag_id = $one;
                $model->save();
            }
            if(isset($arr[$one])){
                unset($arr[$one]);
            }
        }
        BlogTag::deleteAll(['tag_id' => $arr]);
    }
    */

    public function beforeSave($insert)
    {
        if($file = UploadedFile::getInstance($this, 'file')){
            $dir = Yii::getAlias('@images').'/blog/';
            if(file_exists($dir . $this->image) && $this->image){
                unlink($dir . $this->image);
            }
            if(file_exists($dir . '50x50/' . $this->image && $this->image)){
                unlink($dir . '50x50/' . $this->image);
            }
            if(file_exists($dir . '800x/' . $this->image && $this->image)){
                unlink($dir . '800x/' . $this->image);
            }
            $this->image = strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '.' . $file->extension;
            $file->saveAs($dir . $this->image);
            $imag = Yii::$app->image->load($dir . $this->image);
            $imag->background('#fff', 0);
            $imag->resize('50', '50', Image::INVERSE);
            $imag->crop('50', '50');
            $imag->save($dir . '50x50/' . $this->image, 90);
            $imag = Yii::$app->image->load($dir . $this->image);
            $imag->resize('800', NULL, Image::INVERSE);
            $imag->save($dir . '800x/' . $this->image, 90);
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
        if(is_array($this->new_tags)){

            /*
            $old_tags = \yii\helpers\ArrayHelper::map($this->tags,  'id', 'name');
            $insert_tags = array_diff($this->new_tags, $old_tags);
            $delete_tags = array_diff($old_tags, $this->new_tags);
            foreach ($insert_tags as $one_new_tag){
                if($tg = $this->createNewTag($one_new_tag)){
                    Yii::$app->session->addFlash('success', 'Добавлен тег "' . $one_new_tag . '""');
                }else{
                    Yii::$app->session->addFlash('error', 'Тег "' . $one_new_tag. '" не добавился');
                }
            }
            BlogTag::deleteAll(['and', ['blog_id' => $this->id], ['tag_id' => \yii\helpers\ArrayHelper::map($delete_tags,  'id', 'name') ]]);
*/

            $old_tags = \yii\helpers\ArrayHelper::map($this->tags, 'name', 'id');
            foreach ($this->new_tags as $one_new_tag){
                if(isset($old_tags[$one_new_tag])){
                    unset($old_tags[$one_new_tag]);
                }else{
                    if($tg = $this->createNewTag($one_new_tag)){
                        Yii::$app->session->addFlash('success', 'Добавлен тег "' . $one_new_tag . '""');
                    }else{
                        Yii::$app->session->addFlash('error', 'Тег "' . $one_new_tag. '" не добавился');
                    }
                }
            }
            BlogTag::deleteAll(['and', ['blog_id' => $this->id], ['tag_id' => $old_tags]]);


        }else{
            BlogTag::deleteAll(['blog_id' => $this->id]);
        }
    }

    private function createNewTag($new_tag)
    {
        if(!$tag = Tag::find()->andWhere(['name' => $new_tag])->one()){
            $tag = new Tag();
            $tag->name = $new_tag;
            if(!$tag->save()){
                $tag = null;
            }
        }
        if($tag instanceof Tag){
            $blog_tag = new BlogTag();
            $blog_tag->blog_id = $this->id;
            $blog_tag->tag_id = $tag->id;
            if($blog_tag->save()){
                return $blog_tag->id;
            }
        }
        return false;
    }

}