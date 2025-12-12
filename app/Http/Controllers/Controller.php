<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Blog Colaborativo API",
 *     version="1.0.0",
 *     description="API do sistema Blog Colaborativo",
 *     @OA\Contact(
 *         email="contato@blogcolaborativo.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor da API"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Autorização padrão JWT"
 * )
 */
abstract class Controller
{
    //
}

