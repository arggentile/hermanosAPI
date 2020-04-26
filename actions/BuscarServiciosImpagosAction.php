<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\actions;

use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\base\UserException;
use app\models;
use yii\data\ActiveDataProvider;

use app\models\search\GrupoFamiliarSearch;
use app\widgets\buscadorfamilia\BuscadorFamilia;
use app\models\GrupoFamiliar;
use app\models\search\ServicioAlumnoSearch;
use app\models\ServicioAlumno;

use app\helpers\GralException;

class BuscarServiciosImpagosAction extends Action
{
    public function run()
    {       
        try{           
            $searchModel = new ServicioAlumnoSearch();
            $searchModel->load(\Yii::$app->request->getQueryParams());        
            
            $query = ServicioAlumno::find()->distinct();
            $query->joinWith(['alumno al','alumno.grupofamiliar fam', 'alumno.grupofamiliar.responsables.persona per']);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
            /*
            $query->andFilterWhere(['OR',
                        ['like', 'fam.apellidos', $searchModel->familia],   
                        ['like', 'fam.folio', $searchModel->familia])                
                ->andFilterWhere(['like', 'rpe.nombre', $searchModel->responsable]);

            $query->andFilterWhere(['like', 'apellidos', $searchModel->apellidos])                
                ->andFilterWhere(['like', 'folio', $searchModel->folio]);
            */
            return $this->controller->renderAjax('@app/actions/clean/views/clean', ['contenido'=> \app\widgets\buscadorServiciosAlumno\BuscadorServiciosAlumno::widget(['searchModel'=>$searchModel, 'dataProvider'=>$dataProvider])]);
        }catch (GralException $e) { 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));          
            throw new GralException('No se puden mostrar las familias.');                        
        }catch (\Exception $e) { 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));            
            throw new GralException('No se puden mostrar las familias.');                        
        }
    }    
    
}
