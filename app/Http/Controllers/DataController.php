<?php

namespace App\Http\Controllers;

use App\Models\metrics;
use App\Models\shops;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function upload(Request $request)
    {
$shopsInserted = 0;
    $metricsInserted = 0;
    $metricsSkipped = 0;

    // Handle shops.json
    if ($request->hasFile('shops_file')) {
        $shopsJson = json_decode(file_get_contents($request->file('shops_file')->getRealPath()), true);

        foreach ($shopsJson as $shop) {
            $shopModel = shops::firstOrCreate(
                ['shop_id' => $shop['shop_id']], // unique key
                ['shop_name' => $shop['shop_name'], 'region' => $shop['region']]
            );

            if ($shopModel->wasRecentlyCreated) {
                $shopsInserted++;
            }
        }
    }

    // Handle metrics.json
    if ($request->hasFile('metrics_file')) {
        $metricsJson = json_decode(file_get_contents($request->file('metrics_file')->getRealPath()), true);

        foreach ($metricsJson as $metric) {
            // Check if shop exists before inserting metric
            if (shops::where('shop_id', $metric['shop_id'])->exists()) {
                $metricModel = metrics::firstOrCreate(
                    ['shop_id' => $metric['shop_id'], 'date' => $metric['date']], // unique key
                    [
                        'gmv_usd'   => $metric['gmv_usd'],
                        'followers' => $metric['followers'],
                        'ctor'      => $metric['ctor']
                    ]
                );

                if ($metricModel->wasRecentlyCreated) {
                    $metricsInserted++;
                }
            } else {
                $metricsSkipped++;
            }
        }
    }
    // Build message
    $message = "✅ Upload complete. ";
    if ($shopsInserted > 0) {
        $message .= "$shopsInserted new shop(s) added. ";
    }
    if ($metricsInserted > 0) {
        $message .= "$metricsInserted new metric(s) added. ";
    }
    if ($metricsSkipped > 0) {
        $message .= "⚠️ $metricsSkipped metric(s) skipped (shop not found).";
    }

    return redirect()->route('home')->with('success', $message);
    }
}
