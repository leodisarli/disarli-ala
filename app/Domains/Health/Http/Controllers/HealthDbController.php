<?php

namespace App\Domains\Health\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Database\DatabaseManager;

class HealthDbController extends BaseController
{
    private $databaseManager;

    /**
     * constructor
     * @param DatabaseManager $databaseManager
     * @return void
     */
    public function __construct(
        DatabaseManager $databaseManager
    ) {
        $this->databaseManager = $databaseManager;
    }

    /**
     * process the request
     * @return JsonObject
     */
    public function process()
    {
        $this->databaseManager->connection()->getPdo();

        return response()->json(
            [
                'status' => 'online'
            ],
            200
        );
    }
}
