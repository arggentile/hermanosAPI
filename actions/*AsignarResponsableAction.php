<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\actions;

use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\base\UserException;
use backend\models;
use backend\models\PersonaSearch;

use common\widgets\buscadorresponsable\BuscadorResponsable;

/**
 * Acción reutilizable para la búsqueda de legajos
 *
 * @author mboisselier
 */
class AsignarResponsableAction extends Action
{
    public function run()
    {       
        $searchModel = new PersonaSearch();
        $dataProvider = $searchModel->searchResponsables(Yii::$app->request->getQueryParams());
        
        return $this->controller->renderAjax('@common/clean/views/clean', ['contenido'=> BuscadorResponsable::widget(['searchModel'=>$searchModel,'dataProvider'=>$dataProvider])]);
    }    
    
}