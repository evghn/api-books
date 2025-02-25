<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string $description
 * @property string $file
 * @property int $user_id
 * @property int $progress
 * @property int $is_public
 *
 * @property User $user
 */
class Book extends \yii\db\ActiveRecord
{
    const SCENARIO_UPLOAD = 'upload';
    const SCENARIO_PUBLIC = 'public';
    const SCENARIO_PROGRESS = 'progress';

    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_public', 'progress'], 'default', 'value' => 0],
            [['progress'], 'required', 'on' => self::SCENARIO_PROGRESS],
            [['title'], 'required'],
            [['description'], 'string'],
            [['user_id', 'progress'], 'integer'],
            [['author', 'file_html'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 64],
            [['progress'], 'integer', 'min' => 0, 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'html', 'checkExtensionByMimeType' => true, 'maxSize' => 512 * 1024, 'on' => self::SCENARIO_UPLOAD],
            [['is_public'], 'boolean', 'on' => self::SCENARIO_PUBLIC],
            [['is_public'], 'required', 'on' => self::SCENARIO_PUBLIC],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Author',
            'description' => 'Description',
            'file_html' => 'File',
            'user_id' => 'User ID',
            'progress' => 'Progress',
            'is_public' => 'Is Public',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    public function upload()
    {
        if ($this->validate()) {
            $fileName = 'books/'
                . Yii::$app->security->generateRandomString()
                . '.'
                . $this['file']->extension;

            $this['file']->saveAs(
                $fileName
            );

            $this->file_html = $fileName;
            return true;
        } else {
            return false;
        }
    }



    // public static function getBooks()
    // {
    //     $query = self::find()
    //         ->select([
    //             'book.id',
    //             'title',
    //             'author',
    //             'description',
    //             "concat('"
    //                 . Yii::$app->request->hostInfo . "',"
    //                 . "'/', "
    //                 . "file_html) as file_url"
    //         ])
    //         ->innerJoin('user', 'user.id = book.user_id');

    //     if (Yii::$app->user->isGuest) {
    //         $query
    //             ->where(['user.role' => 'admin'])
    //             ->orWhere(['is_public' => 1])

    //         ;
    //     } else {
    //         $query
    //             ->where(['user_id' => Yii::$app->user->id]);
    //     }

    //     if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 'admin') {
    //         $query
    //             ->addSelect(["if (is_public = 1, 'true', 'false') as is_public"])
    //             ->and ;
    //     }

    //     return $query
    //         ->asArray()
    //         ->all()
    //     ;
    // }


    public static function getBook(array $data = [])
    {
        $query = Book::find()
            ->select([
                'book.id',
                'title',
                'author',
                'description',
            ])
            ->innerJoin('user', 'user.id = book.user_id')
            ->asArray();

        if (isset($data['progress'])) {
            $query
                ->addSelect(['progress']);
        }

        $query
            ->addSelect([
                "concat('"
                    . Yii::$app->request->hostInfo . "',"
                    . "'/', "
                    . "file_html) as file_url"
            ]);

        if (isset($data['add_user_id'])) {
            $query
                ->addSelect('user_id');
        }


        $query
            ->andFilterWhere(['book.id' => $data['book_id'] ?? null])
            ->andFilterWhere(['user_id' => $data['user_id'] ?? null])
            ->andFilterWhere(['role' => $data['role'] ?? null])
        ;


        if (isset($data['progress_isset'])) {
            $query
                ->andWhere(['>', 'progress', 0])
                ->andWhere(['<', 'progress', 100])
            ;
        }

        if (isset($data['add_bublic_info'])) {
            $query
                ->addSelect(["if (is_public = 1, 'true', 'false') as is_public"]);
        }

        if (isset($data['add_public_book'])) {
            $query
                ->orWhere(['book.is_public' => 1]);
        }

        if (isset($data['book_id'])) {
            $res = $query->one();
            if (isset($res["is_public"])) {
                $res["is_public"] = $res["is_public"] == 'false' ? false : true;
            }
        } else {
            $res = $query->all();
        }

        return array_map(function ($val) {
            if (isset($val["is_public"])) {
                $val["is_public"] = $val["is_public"] == 'false' ? false : true;
            }
            return $val;
        }, $res);
    }
}
