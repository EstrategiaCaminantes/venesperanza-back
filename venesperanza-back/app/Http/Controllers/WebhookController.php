<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    /**
     * Webhook process
     */
    public function webhookmati(Request $request)
    {
        $web = new Webhook;
        $web->eventname = $request['eventName'];
        $web->resource = $request['resource'];
        $web->step = ($request['step']) ? json_encode($request['step']) : '';
        $web->metadata = ($request['metadata']) ? json_encode($request['metadata']) : '';
        $web->encuesta = ($request['metadata'] && $request['metadata']['user_id']) ? $request['metadata']['user_id'] : '';
        $web->save();
    }
}
