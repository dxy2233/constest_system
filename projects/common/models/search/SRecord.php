<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Record;

/**
 * Article represents the model behind the search form about `common\models\business\BArticle`.
 */
class SRecord extends Record
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sex', 'mobile'], 'integer'],
            [['name', 'idcard'], 'safe'],
        ];
    }

    public $pageSize = 10;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Record::find();

        $config = [
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                        ]
                    ]
                ];


        $config['pagination'] = [
                'pageSize' => $this->pageSize
            ];


        $dataProvider = new ActiveDataProvider($config);
        $query->andFilterWhere([
            'status' => 1
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sex' => $this->sex,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'idcard', $this->idcard]);

        return $dataProvider;
    }
}
