<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->group('/api/v1', function () use ($app){

    $app->get('/places', function () {

        $helper = new \WeddingPlaces\Helpers();

        // api request limit control
        $api_limit = $helper->ApiLimit(60, 10);
        if ( $api_limit["success"] == false )
            return $this->response->withJson($api_limit);
        // api request limit control

        $places = $helper->Read_Csv(__DIR__ . "\\..\\public\\places.csv");
        $sorted_places = $helper->Array_OrderBy($places, 'city', SORT_ASC, 'place', SORT_ASC);

        return $this->response->withJson($sorted_places);
    });

    $app->get('/apilimit', function () {

        $helper = new \WeddingPlaces\Helpers();

        return $this->response->withJson($helper->ApiLimit2(60, 10));
    });

    $app->get('/places/[{city}]', function (Request $request, Response $response, $args) {

        $helper = new \WeddingPlaces\Helpers();

        // api request limit control
        $api_limit = $helper->ApiLimit(60, 10);
        if ( $api_limit["success"] == false )
            return $this->response->withJson($api_limit);
        // api request limit control

        $places = $helper->Read_Csv(__DIR__ . "\\..\\public\\places.csv", $args['city']);
        $sorted_places = $helper->Array_OrderBy($places, 'city', SORT_ASC, 'place', SORT_ASC);

        return $this->response->withJson($sorted_places);
    });

    $app->post('/places', function (Request $request) {

        $helper = new \WeddingPlaces\Helpers();

        // api request limit control
        $api_limit = $helper->ApiLimit(60, 10);
        if ( $api_limit["success"] == false )
            return $this->response->withJson($api_limit);
        // api request limit control

        $params = $request->getParams();
        $result = $helper->Write_Csv(__DIR__ . "\\..\\public\\places.csv", [$params['city'], $params['place']]);

        return $this->response->withJson($result);
    });

    $app->put('/places', function (Request $request) {

        $helper = new \WeddingPlaces\Helpers();

        // api request limit control
        $api_limit = $helper->ApiLimit(60, 10);
        if ( $api_limit["success"] == false )
            return $this->response->withJson($api_limit);
        // api request limit control

        $params = $request->getParams();
        $result = $helper->Update_Csv(__DIR__ . "\\..\\public\\places.csv", $params['old_city'], $params['old_place'], $params['new_city'], $params['new_place']);

        return $this->response->withJson("true");
    });

    $app->delete('/places/[{place}]', function (Request $request, Response $response, $args) {

        $helper = new \WeddingPlaces\Helpers();

        // api request limit control
        $api_limit = $helper->ApiLimit(60, 10);
        if ( $api_limit["success"] == false )
            return $this->response->withJson($api_limit);
        // api request limit control

        $result = $helper->Delete_Csv(__DIR__ . "\\..\\public\\places.csv", explode(",", $args['place'])[0], explode(",", $args['place'])[1]);

        return $this->response->withJson($result);
    });
    
});



